<?php
/**
 * WordPress 內置嵌套評論專用 Ajax comments >> WordPress-jQuery-Ajax-Comments v1.3 by Willin Kan. Optimized Code by Weisay.
 *
 * 說明: 這個文件是由 WP 3.0 根目錄的 wp-comment-post.php 修改的, 修改的地方有注解. 當 WP 升級, 請注意可能有所不同.
 */

session_start();
session_regenerate_id(true); // 增强会话安全性

if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3' ), true ) ) {
		$protocol = 'HTTP/1.0';
	}

	header( 'Allow: POST' );
	header( "$protocol 405 Method Not Allowed" );
	header( 'Content-Type: text/plain' );
	exit;
}

/** Sets up the WordPress Environment. */
require(__DIR__ . '/../../../wp-load.php'); // 此 comments-ajax.php 位於主題資料夾,所以位置已不同

nocache_headers();

// 验证 nonce
$nonce = $_POST['_wpnonce'] ?? '';
if (empty($nonce) || !wp_verify_nonce($nonce, 'comment_nonce')) {
	err(__('安全验证失败，请刷新页面后重试。'));
}

// 评论文章ID验证
$comment_post_ID = isset($_POST['comment_post_ID']) ? absint($_POST['comment_post_ID']) : 0;
if ($comment_post_ID <= 0) {
	err(__('无效的文章ID。'));
}

$post = get_post($comment_post_ID);

if (empty($post->comment_status)) {
	do_action('comment_id_not_found', $comment_post_ID);
	err(__('评论状态无效。'));
}

// get_post_status() will get the parent status for attachments.
$status = get_post_status($post);

$status_obj = get_post_status_object($status);

if ( !comments_open($comment_post_ID) ) {
	do_action('comment_closed', $comment_post_ID);
	err(__('对不起，该文章的评论已关闭。')); // 將 wp_die 改為錯誤提示
} elseif ( 'trash' == $status ) {
	do_action('comment_on_trash', $comment_post_ID);
	err(__('评论状态无效。')); // 將 exit 改為錯誤提示
} elseif ( !$status_obj->public && !$status_obj->private ) {
	do_action('comment_on_draft', $comment_post_ID);
	err(__('评论状态无效。')); // 將 exit 改為錯誤提示
} elseif ( post_password_required($comment_post_ID) ) {
	do_action('comment_on_password_protected', $comment_post_ID);
	err(__('密码保护。')); // 將 exit 改為錯誤提示
} else {
	do_action('pre_comment_on_post', $comment_post_ID);
}

// 清理输入数据
$raw_author = isset($_POST['author']) ? trim($_POST['author']) : null;
$raw_email = isset($_POST['email']) ? trim($_POST['email']) : null;
$raw_url = isset($_POST['url']) ? trim($_POST['url']) : null;
$raw_comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;
$edit_id = isset($_POST['edit_id']) ? absint($_POST['edit_id']) : null;

$comment_author = sanitize_text_field($raw_author ?? '');
$comment_author_email = sanitize_email($raw_email ?? '');
$comment_author_url = esc_url_raw($raw_url ?? '');
$comment_content = wp_kses_post($raw_comment);

// 用户登录处理
$user = wp_get_current_user();
if ($user->exists()) {
	if (empty($user->display_name)) {
		$user->display_name = $user->user_login;
	}
	$comment_author = $user->display_name;
	$comment_author_email = $user->user_email;
	$comment_author_url = $user->user_url;

	if (current_user_can('unfiltered_html')) {
		if (!isset($_POST['_wp_unfiltered_html_comment']) || 
			!wp_verify_nonce($_POST['_wp_unfiltered_html_comment'], 'unfiltered-html-comment_' . $comment_post_ID)) {
			kses_remove_filters();
			kses_init_filters();
		}
	}
} elseif (get_option('comment_registration') || 'private' === $status) {
	err(__('对不起，您必须登录后才能发表评论。'));
}

// 必填字段验证
if (get_option('require_name_email') && !$user->exists()) {
	if ($raw_author === '' || is_null($raw_author)) {
		err(__('错误：请填写您的昵称。'));
	}
	if ($raw_email === '' || is_null($raw_email)) {
		err(__('错误：请填写您的邮箱地址。'));
	}
	if (!is_email($raw_email)) {
		err(__('错误：请输入有效的邮箱地址。'));
	}
}
if ($raw_comment === '' || is_null($raw_comment)) {
	err(__('错误：请输入评论内容。'));
}

// 评论重复检查
$dupe = $wpdb->prepare(
	"SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_author = %s ",
	$comment_post_ID, $comment_author
);
if ($comment_author_email) {
	$dupe .= $wpdb->prepare("OR comment_author_email = %s ", $comment_author_email);
}
$dupe .= $wpdb->prepare(") AND comment_content = %s LIMIT 1", $comment_content);

if ($wpdb->get_var($dupe)) {
	err(__('发现重复的评论，它看起来好像您已经说过了！'));
}

// 评论频率限制检查
$lasttime = $wpdb->get_var($wpdb->prepare(
	"SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author = %s ORDER BY comment_date DESC LIMIT 1",
	$comment_author
));
if ($lasttime) {
	$time_lastcomment = mysql2date('U', $lasttime, false);
	$time_newcomment = mysql2date('U', current_time('mysql', 1), false);
	$flood_die = apply_filters('comment_flood_filter', false, $time_lastcomment, $time_newcomment);
	if ($flood_die) {
		err(__('您提交评论的速度太快了，请稍后再发表评论。'));
	}
}

// 评论父级ID验证
$comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
if ($comment_parent > 0) {
	$parent_comment = get_comment($comment_parent);
	if (!$parent_comment || $parent_comment->comment_post_ID != $comment_post_ID) {
		err(__('无效的父级评论。'));
	}
}
$comment_type = isset($comment_type) ? $comment_type : '';
$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

// 垃圾评论禁止提交
if (!is_user_logged_in()) { // 登录用户跳过检查
	if (wp_check_comment_disallowed_list(
		$commentdata['comment_author'],
		$commentdata['comment_author_email'],
		$commentdata['comment_author_url'],
		$commentdata['comment_content'],
		$_SERVER['REMOTE_ADDR'],
		$_SERVER['HTTP_USER_AGENT']
	)) {
		err(__('禁止发表评论！'));
	}
}

// 评论编辑/新建处理
if ($edit_id) {
	if (!isset($_SESSION['comment_id']) || $_SESSION['comment_id'] != $edit_id) {
		err(__('您没有权限编辑该评论！'));
	}
	$comment_id = $commentdata['comment_ID'] = $edit_id;
	wp_update_comment($commentdata);
} else {
	$comment_id = wp_new_comment($commentdata);
	$_SESSION['comment_id'] = $comment_id;
}

$comment = get_comment($comment_id);
do_action('set_comment_cookies', $comment, $user);

// 错误提示功能
function err($ErrMsg) {
	header('HTTP/1.1 405 Method Not Allowed');
	echo esc_html($ErrMsg);
	exit;
}

// 计算评论深度
$comment_depth = 1;
$tmp_c = $comment;
while ($tmp_c->comment_parent != 0) {
	$comment_depth++;
	$tmp_c = get_comment($tmp_c->comment_parent);
	if ($comment_depth > 10) break; // 防止无限循环
}

//以下是評論式樣, 不含 "回覆". 要用你模板的式樣 copy 覆蓋.
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<div class="comment-avatar vcard"><?php echo get_avatar( $comment->comment_author_email, 60, '', get_comment_author() ); ?></div>
	<div class="comment-box">
		<div class="fn comment-name"><?php printf( __( '<cite class="fn">%s</cite>'), get_comment_author_link() ); ?><?php edit_comment_link('编辑','&nbsp;&nbsp;',''); ?></div>
		<div class="comment-content">
		<?php if ( $comment->comment_approved == '0' ) : ?>
		<p class="comment-approved">您的评论正在等待审核中...</p>
		<?php endif; ?>
		<?php comment_text() ?>
		</div>
		<div class="clear"></div>
		<div class="comment-info">
		<span class="datetime"><?php comment_date('Y-m-d') ?> <?php comment_time() ?> </span>
		</div>
	</div>
</div>
