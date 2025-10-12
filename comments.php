<?php
// Do not delete these lines
if ( !empty($_SERVER['SCRIPT_FILENAME'] ) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die('请勿直接加载此页。谢谢！');
}
if ( post_password_required() ) {
	echo '<h3 class="article-title">必须输入密码，才能查看评论！</h3>';
	return;
}
?>
<?php
// 生成ajax分页用的 nonce
$comment_paging_nonce = wp_create_nonce('comment_paging_nonce');
?>
<div id="comments" class="comments-area">
<?php if ( have_comments() ) : ?>
<h3 class="article-title"><span class="comment-title"><?php the_title(); ?>：</span>目前有 <?php comments_number('', '1 条', '% 条' );?>评论</h3>
<div id="comment-ajax">
<ol class="comment-list">
<?php wp_list_comments('type=comment&callback=weisay_comment&end-callback=weisay_end_comment&max_depth=' .get_option('thread_comments_depth'). ' '); ?>
</ol>
<div class="pagination" id="commentpager"><?php paginate_comments_links(); ?></div>
</div>
<script type="text/javascript">var commentPagingNonce = '<?php echo esc_js( $comment_paging_nonce ); ?>';</script>
<?php endif; // if ( have_comments() ) ?>
<?php if ( ('0' == $post->comment_count) && comments_open() ) : ?>
<h3 class="article-title"><span class="comment-title"><?php the_title(); ?>：</span>等您坐沙发呢！</h3>
<?php endif; // if ( ('0' == $post->comment_count) && comments_open() ) ?>
<?php if ( comments_open() ) : ?>
<div id="respond" class="comment-respond">
<h3 id="reply-title" class="comment-reply-title">发表评论</h3><small><?php cancel_comment_reply_link('[点击取消回复]'); ?></small>
<?php
switch (weisay_option('wei_gravatar')) {
	case 'two':
		$gravatarurl = 'https://cravatar.cn/avatar/';
		break;
	case 'three':
		$gravatarurl = 'https://gravatar.loli.net/avatar/';
		break;
	case 'four':
		$gravatarurl = 'https://cdn.sep.cc/avatar/';
		break;
	default:
		$gravatarurl = 'https://weavatar.com/avatar/';
}
?>
<script type="text/javascript">
var gravatarurl = "<?php echo esc_url($gravatarurl); ?>";
jQuery(document).ready(function ($) {
	$('#email').on('blur', function () {
		let email = $('#email').val().trim();
		if (email !== '') {
			let hash = sha256(email.toLowerCase());
			$('#real-avatar .avatar').attr('src', gravatarurl + hash + '?s=50&d=mm&r=g');
			$('#real-avatar .avatar').attr('srcset', gravatarurl + hash + '?s=100&d=mm&r=g 2x');
		}
	});
});
</script>
<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
<div class="must-log-in"><?php print '您必须'; ?> <a href="<?php echo esc_url( wp_login_url(get_permalink()) ); ?>">[ 登录 ]</a> 才能发表评论！</div>
<?php else : ?>
<form action="<?php echo esc_url( get_template_directory_uri() . '/com-post-ajax.php' ); ?>" method="post" id="commentform" class="comment-form">
<p class="comment-notes"><span id="email-notes">电子邮件地址不会被公开。</span> 必填项已用 <span class="required">*</span> 标注</p>
<div class="comment-frame">  
<div id="real-avatar" class="comment-author-avatar">
<?php if ( is_user_logged_in() ) : ?>
<?php $current_user = wp_get_current_user(); echo get_avatar( $current_user->user_email, 48, '', $current_user->display_name ); ?>
<?php elseif ( isset($_COOKIE['comment_author_email_'.COOKIEHASH]) ) : ?>
<?php echo get_avatar( $comment_author_email, 48, '', 'gravatar' );?>
<?php else : ?>
<?php global $user_email;?><?php echo get_avatar( $user_email, 48, '', 'gravatar' ); ?>
<?php endif; // if ( is_user_logged_in() ) ?>
</div>
<div class="comment-post">
<?php if ( is_user_logged_in() ) : ?>
<div class="comment-author"><?php print '登录者：'; ?> <a href="<?php bloginfo('url'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>&nbsp;&nbsp;<a href="<?php echo esc_url( wp_logout_url(get_permalink()) ); ?>" title="退出" class="comment-change">[ 退出 ]</a></div>
<?php elseif ( '' != $comment_author ) : ?>
<div class="comment-author"><?php printf(__('<strong>%s</strong> 您好，欢迎回来！'), esc_html( $comment_author ) ); ?>
<a href="javascript:toggleCommentAuthorInfo();" id="toggle-comment-author-info" class="comment-change">[ 更改 ]</a></div>
<script type="text/javascript">
const changeMsg="[更改]",closeMsg="[隐藏]";function toggleCommentAuthorInfo(){$('#comment-author-info').slideToggle('slow',function(){$(this).is(':visible')?$('#toggle-comment-author-info').text(closeMsg):$('#toggle-comment-author-info').text(changeMsg);});}$(function(){$('#comment-author-info').hide();});
</script>
<?php endif; // if ( is_user_logged_in() ) ?>
<?php if ( !is_user_logged_in() ) : ?>
<div id="comment-author-info" class="comment-author-info">
<p class="comment-input">
<label for="author" class="required"><i class="iconfont icon-aria-username"></i></label>
<input placeholder="昵称 *" type="text" name="author" id="author" class="text" value="<?php echo $comment_author; ?>" />
</p>
<p class="comment-input">
<label for="email"  class="required"><i class="iconfont icon-aria-email"></i></label>
<input placeholder="邮箱 *" type="email" name="email" id="email" class="text" value="<?php echo $comment_author_email; ?>" />
</p>
<p class="comment-input">
<label for="url"><i class="iconfont icon-aria-link"></i></label>
<input placeholder="网站" type="url" name="url" id="url" class="text" value="<?php echo $comment_author_url; ?>" />
</p>
</div>
<?php endif; // if ( !is_user_logged_in() ) ?>
<div class="comment-emoji">
<p class="emoji-post"><a class="emoji" href="javascript:void(0)" title="插入表情"><i class="iconfont emojiicon">&#xe681;</i></a></p>
<p class="emoji-smilies"><?php require get_template_directory() . '/includes/smilies.php'; ?></p>
</div>
<textarea name="comment" id="comment" placeholder="互动可以先从评论开始…" ></textarea>
<p class="form-submit">
<input id="submit" class="submit" name="submit" type="submit" value="提交评论" />
<?php wp_nonce_field('comment_nonce', '_wpnonce', false); // 在评论表单中添加 nonce ?>
<?php comment_id_fields(); do_action('comment_form', $post->ID); ?>
</p>
</div>
</div>
</form>
<div class="clear"></div>
<?php endif; // if ( get_option('comment_registration') && !is_user_logged_in() ) ?>
</div>
<?php endif; // if ( comments_open() ) ?>
<?php if ( ! comments_open() ) : ?>
<h3 class="article-title">报歉！评论已关闭。</h3>
<?php endif; // if ( ! comments_open() ) ?>
</div>