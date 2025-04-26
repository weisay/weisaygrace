<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('请勿直接加载此页。谢谢！');
	if ( post_password_required() ) { ?>
	<h3 class="article-title">必须输入密码，才能查看评论！</h3>
	<?php
		return;
	}
?>
<div id="comments" class="comments-area">
<?php if ( have_comments() ) : ?>
<h3 class="article-title"><span class="comment-title"><?php the_title(); ?>：</span>目前有 <?php comments_number('', '1 条', '% 条' );?>评论</h3>
<span id="cp_post_id" style="display:none;"><?php the_ID(); ?></span>
<div id="pagetext">
<ol class="comment-list">
<?php wp_list_comments('type=comment&callback=weisay_comment&end-callback=weisay_end_comment&max_depth=' .get_option('thread_comments_depth'). ' '); ?>
</ol>
<div class="pagination" id="commentpager"><?php paginate_comments_links(); ?></div>
</div>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function comment_page_ajas(){
	jQuery('#commentpager a').click(function(){
		var post_id = jQuery('#cp_post_id').html() //当前文章ID-- post_id
		//文章ID获得
		//获取欲取得的评论页页码
		var compageUrl = jQuery(this).attr("href");
		var page_id = compageUrl.match(/page[\-|=][0-9]{1,4}/);
		var arr_temp_1 = page_id[0].split(/\-|=/);
		var page_id = arr_temp_1[1]; 
		jQuery.ajax({
			url: compageUrl,
			type:"POST",
			data:"action=compageajax&postid="+post_id+"&pageid="+page_id,
			beforeSend:function() {
				document.body.style.cursor = 'wait';   
				if (jQuery("#cancel-comment-reply-link")) // 取消未回复的评论
					jQuery("#cancel-comment-reply-link").trigger("click");
					jQuery('#commentpager').html('<em class="ajaxcomm">正在努力为您载入中...</em>');
			},
			error:function (xhr, textStatus, thrownError) { 
				alert("readyState: " + xhr.readyState + " status:" + xhr.status + " statusText:" + xhr.statusText +" responseText:" +xhr.responseText + " responseXML:" + xhr.responseXML + " onreadystatechange" +xhr.onreadystatechange);         
				alert(thrownError);
			},
			success: function (data) {
				jQuery('#pagetext').html(data);
				document.body.style.cursor = 'auto';
				jQuery('html,body').animate({scrollTop:jQuery('#comments').offset().top}, 800);
				comment_page_ajas();
			}
		});
		return false;
	});
})
// ]]>
</script>
<?php endif; // have_comments() ?>
<?php if ( ('0' == $post->comment_count) && comments_open() ) : ?>
<h3 class="article-title"><span class="comment-title"><?php the_title(); ?>：</span>等您坐沙发呢！</h3>
<?php endif; ?>
<?php if ( comments_open() ) : ?>
<div id="respond" class="comment-respond">
<h3 id="reply-title" class="comment-reply-title">发表评论</h3><small><?php cancel_comment_reply_link('[点击取消回复]'); ?></small>
<?php if (weisay_option('wei_gravatar') == 'two') : ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
var gravatarurl= 'https://cravatar.cn/avatar/';
jQuery('#email').blur(function() {
jQuery('#real-avatar .avatar').attr('src', gravatarurl + hex_md5(jQuery('#email').val()) + '?s=50&d=mm&r=g');
jQuery('#real-avatar .avatar').attr('srcset', gravatarurl + hex_md5(jQuery('#email').val()) + '?s=100&d=mm&r=g 2x');
jQuery('#Get_Gravatar').fadeOut().html('看看右边头像对不对？').fadeIn('slow');
});
});
//]]>
</script>
<?php elseif (weisay_option('wei_gravatar') == 'three') : ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
var gravatarurl= 'https://gravatar.loli.net/avatar/';
jQuery('#email').blur(function() {
jQuery('#real-avatar .avatar').attr('src', gravatarurl + hex_md5(jQuery('#email').val()) + '?s=50&d=mm&r=g');
jQuery('#real-avatar .avatar').attr('srcset', gravatarurl + hex_md5(jQuery('#email').val()) + '?s=100&d=mm&r=g 2x');
jQuery('#Get_Gravatar').fadeOut().html('看看右边头像对不对？').fadeIn('slow');
});
});
//]]>
</script>
<?php elseif (weisay_option('wei_gravatar') == 'four') : ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
var gravatarurl= 'https://cdn.sep.cc/avatar/';
jQuery('#email').blur(function() {
jQuery('#real-avatar .avatar').attr('src', gravatarurl + hex_md5(jQuery('#email').val()) + '?s=50&d=mm&r=g');
jQuery('#real-avatar .avatar').attr('srcset', gravatarurl + hex_md5(jQuery('#email').val()) + '?s=100&d=mm&r=g 2x');
jQuery('#Get_Gravatar').fadeOut().html('看看右边头像对不对？').fadeIn('slow');
});
});
//]]>
</script>
<?php else: ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
var gravatarurl= 'https://weavatar.com/avatar/';
jQuery('#email').blur(function() {
jQuery('#real-avatar .avatar').attr('src', gravatarurl + hex_md5(jQuery('#email').val()) + '?s=50&d=mm&r=g');
jQuery('#real-avatar .avatar').attr('srcset', gravatarurl + hex_md5(jQuery('#email').val()) + '?s=100&d=mm&r=g 2x');
jQuery('#Get_Gravatar').fadeOut().html('看看右边头像对不对？').fadeIn('slow');
});
});
//]]>
</script>
<?php endif; ?>
	<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
<div class="must-log-in"><?php print '您必须'; ?><a href="<?php bloginfo('url'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"> [ 登录 ] </a>才能发表评论！</div>
	<?php else : ?>
<form action="<?php bloginfo('url'); ?>/wp-comments-post.php" method="post" id="commentform" class="comment-form">
	<p class="comment-notes"><span id="email-notes">电子邮件地址不会被公开。</span> 必填项已用 <span class="required">*</span> 标注</p>
	<?php if ( is_user_logged_in() ) : ?>
	<div class="comment-author"><?php print '登录者：'; ?> <a href="<?php bloginfo('url'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>&nbsp;&nbsp;<a href="<?php echo wp_logout_url(get_permalink()); ?>" title="退出" class="comment-change"><?php print '[ 退出 ]'; ?></a></div>
	<?php elseif ( '' != $comment_author ): ?>
	<div class="comment-author"><?php printf(__('欢迎回来 <strong>%s</strong>'), $comment_author); ?>
	<a href="javascript:toggleCommentAuthorInfo();" id="toggle-comment-author-info" class="comment-change">[ 更改 ]</a></div>
<script type="text/javascript" charset="utf-8">
	//<![CDATA[
	var changeMsg = "[ 更改 ]";
	var closeMsg = "[ 隐藏 ]";
	function toggleCommentAuthorInfo() {
		jQuery('#comment-author-info').slideToggle('slow', function(){
		if ( jQuery('#comment-author-info').css('display') == 'none' ) {
			jQuery('#toggle-comment-author-info').text(changeMsg);
			} else {
			jQuery('#toggle-comment-author-info').text(closeMsg);
			}
		});
	}
	jQuery(document).ready(function(){
		jQuery('#comment-author-info').hide();
	});
	//]]>
</script>
	<?php endif; ?>
	<div class="comment-frame">  
		<div id="real-avatar" class="comment-author-avatar">
			<?php if ( is_user_logged_in() ) : ?>
				<?php $current_user = wp_get_current_user(); echo get_avatar( $current_user->user_email, 48, '', $current_user->display_name ); ?>
			<?php elseif(isset($_COOKIE['comment_author_email_'.COOKIEHASH])) : ?>
				<?php echo get_avatar( $comment_author_email, 48, '', 'gravatar' );?>
			<?php else: ?>
				<?php global $user_email;?><?php echo get_avatar( $user_email, 48, '', 'gravatar' ); ?>
			<?php endif; ?>
		</div>
		<div class="comment-post">
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
			<input type="url" name="url" id="url" class="text" placeholder="网站" value="<?php echo $comment_author_url; ?>" />
			</p>
		</div>
		<?php endif; ?>
			<div class="comment-emoji">
				<p class="emoji-post"><a class="emoji" href="javascript:void(0)" title="插入表情"><i class="iconfont emojiicon">&#xe681;</i></a></p>
				<p class="emoji-smilies"><?php include('includes/smilies.php'); ?></p>
			</div>
			<textarea name="comment" id="comment" placeholder="互动可以先从评论开始…" ></textarea>
			<p class="form-submit">
				<input id="submit" class="submit" name="submit" type="submit" value="提交<?php if ( is_page(2)) : ?>留言<?php else: ?>评论<?php endif; ?>" />
				<?php comment_id_fields(); do_action('comment_form', $post->ID); ?>
			</p>
		</div>
	</div>
</form>
<div class="clear"></div>
<?php endif; // If registration required and not logged in ?>
</div>
<?php endif; // comments_open() ?>
<?php if ( ! comments_open() ) : ?>
<h3 class="article-title">报歉！评论已关闭。</h3>
<?php endif; ?>
</div>