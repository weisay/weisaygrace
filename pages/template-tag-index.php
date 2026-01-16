<?php
/*
Template Name: 标签索引
*/
?>
<?php get_header(); ?>
<script type="text/javascript">
jQuery(document).on('click','.tag-index a[href^="#"]',function(e){e.preventDefault();var href=jQuery(this).attr('href');var targetId=href;var selector='#'+CSS.escape(targetId.substring(1));var $target=jQuery(selector);if($target.length){var pos=$target.offset().top;jQuery('html, body').animate({scrollTop:pos},400)}});
</script>
<div class="container">
<div class="main main-all">
<div class="crumb">当前位置： <a title="返回首页" href="<?php echo home_url('/'); ?>">首页</a> &gt; <h1><?php the_title(); ?></h1></div>
<div class="article page-tag">
<?php tag_groups_html(); ?>
</div>
</div>
</div>
<?php get_footer(); ?>