<?php
/*
Template Name: 标签云集
*/
?>
<?php get_header(); ?>
<div class="container">
<div class="main main-all">
<div class="crumb">当前位置： <a title="返回首页" href="<?php bloginfo('url'); ?>/">首页</a> &gt; <h1><?php the_title(); ?></h1></div>
<div class="article page-tag">
<?php echo tag_cloud_list(); ?>
</div>
</div>
</div>
<?php get_footer(); ?>