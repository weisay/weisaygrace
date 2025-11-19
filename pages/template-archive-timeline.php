<?php
/*
Template Name: 时间轴归档
*/
?>
<?php get_header(); ?>
<div class="container">
<div class="main main-all">
<div class="crumb">当前位置： <a title="返回首页" href="<?php bloginfo('url'); ?>/">首页</a> &gt; <h1><?php the_title(); ?></h1></div>
	<div class="article">
	<div class="archives-content">
	<?php echo timeline_archive() ?>
	</div>
	</div>
</div>
</div>
<?php get_footer(); ?>