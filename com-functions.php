<?php
/**
 * 单独整理评论相关函数.
 */

//中文评论检查
if (weisay_option('wei_chinese') == 'open') {
	add_filter('preprocess_comment', 'verify_chinese_comment');
	function verify_chinese_comment($commentdata) {
		$content = $commentdata['comment_content'];
		if (!preg_match('/\p{Han}/u', $content)) {
			$is_ajax = wp_doing_ajax()
				|| (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
					&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
			if ($is_ajax) {
				status_header(400);
				header('Content-Type: text/plain; charset=utf-8');
				echo '评论必须包含中文内容！';
				exit;
			}
			wp_die(
				'Invalid comment content.',
				'Comment Error',
				array('response' => 400)
			);
		}
		return $commentdata;
	}
}

//评论者网站新窗口打开
add_filter('get_comment_author_link', function ($return, $author, $id) {
	return str_replace('<a ', '<a target="_blank" ', $return);
},0,3 );

//评论回复自动添加@评论者
function wei_comment_add_at( $comment_text, $comment = '') {
	if(isset($comment->comment_parent) && $comment->comment_parent > 0) {
		$comment_text = '<a rel="nofollow" class="comment_at" href="#comment-' . $comment->comment_parent . '">@'.get_comment_author( $comment->comment_parent ) . '</a>' . $comment_text;
	}
	return $comment_text;
}
add_filter( 'comment_text' , 'wei_comment_add_at', 20, 2);

//计算评论楼层
function calculate_comment_count() {
	global $commentcount, $wpdb, $post;
	// 如果已经计算过，直接返回
	if (!empty($commentcount)) {
		return $commentcount;
	}
	// 获取评论排序方式和当前页面信息
	$comorder = get_option('comment_order');
	$page = max(0, absint(get_query_var('cpage'))); // 获取当前评论页码，防止负数
	$cpp = absint(get_option('comments_per_page')); // 获取每页评论数
	// 计算楼层（分页显示评论时的序号）
	if ($comorder == 'asc') {
		// 旧的评论在页面顶部
		$page = ($page > 0) ? $page - 1 : 0;
		$commentcount = $cpp * $page;
	} else {
		// 新的评论在页面顶部
		$post_id = absint($post->ID);
		$cnt = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_post_ID = %d AND comment_type IN ('', 'comment') AND comment_approved = '1' AND comment_parent = 0",
				$post_id
			)
		);
		$cnt = absint($cnt); // 获取主评论总数量
		$total_pages = ceil($cnt / $cpp); // 计算总页数
		// 如果是最后一页或者只有一页，则从主评论总数开始
		if ($total_pages == 1 || ($page > 1 && $page == $total_pages)) {
			$commentcount = $cnt + 1;
		} else {
			$commentcount = ($cpp * $page) + 1;
		}
	}
	$commentcount = absint($commentcount); // 确保评论计数为正整数
	return $commentcount;
}

//评论
function weisay_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	global $commentcount, $post;
	$commentcount = calculate_comment_count();
?>
<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
<?php $add_below = 'div-comment'; ?>
<div class="comment-avatar vcard"><?php echo get_avatar( $comment->comment_author_email, 60, '', get_comment_author() ); ?></div>
<div class="comment-box">
<?php if ( $comment->comment_approved == '1' ) : ?>
<span class="floor"><?php
if (!$comment->comment_parent) { // 只处理主评论
$comorder = get_option('comment_order');
if ($comorder == 'asc') {
	// 正序排列 - 旧评论在前
	switch ($commentcount) {
		case 0: echo "沙发"; $commentcount++; break;
		case 1: echo "板凳"; $commentcount++; break;
		case 2: echo "地板"; $commentcount++; break;
		default: printf('%1$s楼', ++$commentcount);
	}
} else {
	// 倒序排列 - 新评论在前
	switch ($commentcount) {
		case 2: echo "沙发"; $commentcount--; break;
		case 3: echo "板凳"; $commentcount--; break;
		case 4: echo "地板"; $commentcount--; break;
		default: printf('%1$s楼', --$commentcount);
	}
}
}
?></span><?php endif; ?>
<div class="fn comment-name"><?php comment_author_link() ?><?php if ( is_active_sidebar( 'sidebar-7' ) ) : ?><?php dynamic_sidebar( 'sidebar-7' ); ?><?php endif; ?>：<?php if(function_exists('wpua_custom_output')) { wpua_custom_output(); } ?></div>
<?php if( (weisay_option('wei_touching') == 'open') && ( $comment->comment_karma == '1' )) : ?><div class="touching-comments-chosen"><?php
$touchingUrl = weisay_option('wei_touchingurl');
if ($touchingUrl) {
	echo '<a href="' . $touchingUrl . '" target="_blank"><span>入选走心评论</span></a>';
} else {
	echo '<span>入选走心评论</span>';
}
?></div><?php endif; ?>
<div class="comment-content">
<?php if ( $comment->comment_approved == '0' ) : ?>
<p class="comment-approved">您的评论正在等待审核中...</p>
<?php endif; ?>
<?php comment_text() ?>
</div>
<div class="comment-info">
<span class="datetime"><?php comment_date('Y-m-d') ?> <span class="date-hi"><?php comment_date('H:i') ?></span></span>
<?php if(current_user_can('manage_options')) : ?><span class="ip-location">来自<?php echo convertip(get_comment_author_ip()); ?></span>
<?php elseif (weisay_option('wei_ipshow') == 'display'): ?><span class="ip-location">来自<?php echo convertipsimple(get_comment_author_ip()); ?></span><?php endif; ?>
<?php edit_comment_link('<span class="comment-edit"><i class="iconfont editicon">&#xe647;</i>编辑</span>','',''); ?>
<span class="reply">
<?php 
$replyButton = get_comment_reply_link(array_merge( $args, array('reply_text' => '<i class="iconfont replyicon">&#xe6ec;</i>回复', 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'])));
$replyButton = is_string($replyButton) ? str_replace(
	'data-belowelement',
	'onclick="return addComment.moveForm( \'div-comment-'.get_comment_ID().'\', \''.get_comment_ID().'\', \'respond\', \''.get_the_ID().'\', false, this.getAttribute(\'data-replyto\') )" data-belowelement',
	$replyButton
) : $replyButton;
echo $replyButton;
?>
</span>
<?php if (weisay_option('wei_touching') == 'open' && current_user_can('manage_options')) : ?>
<span class="touching-comments-button"><a class="karma-link" data-karma="<?php echo $comment->comment_karma; ?>" href="<?php echo wp_nonce_url( home_url('/comment-karma'), 'KARMA_NONCE' ); ?>" onclick="return post_karma(<?php comment_ID(); ?>, this.href, this)">
<?php if ($comment->comment_karma == 0) {
echo '<i class="iconfont hearticon" title="加入走心">&#xe602;</i>';
} else {
echo '<i class="iconfont hearticon" title="取消走心">&#xe601;</i>';
}
?></a></span>
<?php endif; ?>
</div>
</div>
</div>
<?php
}
function weisay_end_comment() {	echo '</li>'; }

//走心评论独立页面使用
function weisay_touching_comments_list($comment) {
	$cpage = get_page_of_comment( $comment->comment_ID, $args = array() );
?>
<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
<?php $add_below = 'div-comment'; ?>
<div class="comment-info">
<div class="comment-author">
<p class="fn comment-name"><?php comment_author_link(); ?></p>
<p class="comment-datetime"><?php comment_date('Y-m-d'); ?></p>
</div>
<div class="comment-avatar vcard"><?php echo get_avatar( $comment->comment_author_email, 60, '', get_comment_author() ); ?></div>
</div>
<div class="comment-content"><?php comment_text() ?></div>
<div class="comment-from">评论于<span class="bullet">•</span><a href="<?php echo get_comment_link($comment->comment_ID, $cpage); ?>" target="_blank"><?php echo get_the_title($comment->comment_post_ID); ?></a></div>
</div><div class="clear"></div>
<?php
}
function weisay_touching_comments_end_list() { echo '</li>'; }

/**
 * 处理走心评论
 * POST /comment-karma
 * 提交三个参数
 *  comment_karma: 0 或者 1
 *  comment_id: 评论ID
 *  _wpnonce: 避免意外提交
 */
function weisay_touching_comments_karma_request() {
	// Check if we're on the correct url
	global $wp;
	$current_slug = add_query_arg( array(), $wp->request );
	if($current_slug !== 'comment-karma') {
		return false;
	}

	global $wp_query;
	if ($wp_query->is_404) {
		$wp_query->is_404 = false;
	}

	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: application/json; charset=utf-8');

	$result = array(
		'code'=> 403,
		'message'=> 'Login required.'
	);

	if (!is_user_logged_in() || !current_user_can('manage_options')) {
		// 未认证的用户拒绝继续执行请求
		header("HTTP/1.1 403 Forbidden");
		wp_die(json_encode($result));
	}

	if (empty($_SERVER['REQUEST_METHOD']) ||
		strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' ||
		empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
		strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
		$result['message'] = 'Request method not allowed';
		header("HTTP/1.1 403 Forbidden");
		wp_die(json_encode($result));
	}

	// Check if it's a valid request.
	$nonce = $_REQUEST['_wpnonce'] ?? '';
	if (empty($nonce) || ! wp_verify_nonce($nonce, 'KARMA_NONCE')) {
		$result['message'] = 'Security Check';
		header('HTTP/1.1 403 Forbidden');
		wp_die(json_encode($result));
	}

	// Do your stuff here
	$comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
	$comment_karma = isset($_POST['comment_karma']) ? intval($_POST['comment_karma']) : 0;
	if ($comment_id <= 0) {
		$result['code'] = 501;
		$result['message'] = 'Incorrect parameter';
		header('HTTP/1.1 500 Internal Server Error');
		wp_die(json_encode($result));
	}

	// 更新数据库
	$comment_data = array();
	$comment_data['comment_ID'] = intval($comment_id);
	$comment_data['comment_karma'] = intval($comment_karma);
	
	if (wp_update_comment( $comment_data )) {
		$result['code'] = 200;
		$result['message'] = 'ok';
		header("HTTP/1.1 200 OK");
	} else {
		$result['code'] = 502;
		$result['message'] = 'comment update failed';
		header("HTTP/1.1 500 Internal Server Error");
	}

	exit(json_encode($result));
}

add_action( 'template_redirect', 'weisay_touching_comments_karma_request', 0);

//评论翻页Ajax
function AjaxCommentsPage() {
	if ( isset($_POST['action']) && $_POST['action'] === 'compageajax' ) {
		// 只允许 POST 请求
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			die('Method not allowed.');
		}
		// 验证 Nonce
		check_ajax_referer( 'comment_paging_nonce', 'nonce_field' );
		// 安全过滤输入
		$postid = isset($_POST['postid']) ? absint($_POST['postid']) : 0;
		$pageid = isset($_POST['pageid']) ? absint($_POST['pageid']) : 1;
		// postid 必须有效
		if ( $postid <= 0 ) {
			wp_die( esc_html__( 'Invalid post ID.', 'theme-textdomain' ) );
		}
		// 构造 Post 对象
		$post = new stdClass();
		$post->ID = $postid;
		// 处理为顺序输出
		$order = 'ASC';
		global $wp_query, $wpdb, $user_ID;
		// 获取当前评论者信息
		$commenter = wp_get_current_commenter();
		$comment_author = $commenter['comment_author'];
		$comment_author_email = $commenter['comment_author_email'];
		// 根据登录/匿名状态获取评论
		if ( $user_ID ) {
			$comments = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM $wpdb->comments
					WHERE comment_post_ID = %d
					AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ))
					ORDER BY comment_date_gmt $order",
					$post->ID,
					$user_ID
				)
			);
		} elseif ( empty( $comment_author ) ) {
			$comments = get_comments(
				array(
					'post_id' => $post->ID,
					'status' => 'approve',
					'order' => $order
				)
			);
		} else {
			$comments = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM $wpdb->comments
					WHERE comment_post_ID = %d
					AND ( comment_approved = '1'
					OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ))
					ORDER BY comment_date_gmt $order",
					$post->ID,
					wp_specialchars_decode( $comment_author, ENT_QUOTES ),
					$comment_author_email
				)
			);
		}
		$wp_query->comments = apply_filters( 'comments_array', $comments, $post->ID );
		$wp_query->comment_count = count( $wp_query->comments );
		update_comment_cache( $wp_query->comments );
		$max_depth = absint( get_option('thread_comments_depth', 10) );
		// 评论分页参数
		$args = array(
			'current' => $pageid,
			'echo' => false,
			'type' => ''
		);
		// 输出评论列表
		echo '<ol class="comment-list">';
		echo wp_list_comments(
			array(
				'type' => 'comment',
				'callback' => 'weisay_comment',
				'end-callback' => 'weisay_end_comment',
				'max_depth' => $max_depth,
				'page' => $pageid,
			),
			$wp_query->comments
		);
		echo '</ol><div class="pagination comment-navigation" id="commentpager">';
		$comment_pages = paginate_comments_links( $args );
		echo $comment_pages . '</div>';
		die();
	}
}
add_action( 'template_redirect', 'AjaxCommentsPage' );