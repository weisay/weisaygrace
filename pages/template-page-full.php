<?php
/*
Template Name: 默认模板-无侧栏
*/
?>
<?php get_header(); ?>
<div class="container">
<div class="main main-all">
<div class="crumb">当前位置： <a title="返回首页" href="<?php echo home_url('/'); ?>">首页</a> &gt; <?php the_title(); ?></div>
<div class="article">
<h1 class="article-title"><?php the_title(); ?></h1>
<?php if ( is_active_sidebar( 'sidebar-0' ) ) : ?>
<?php dynamic_sidebar( 'sidebar-0' ); ?>
<?php endif; ?>
<div class="article-content article-index-area" itemprop="articleBody">
<?php the_content('Read more...'); ?>
</div>
</div>
<div class="article">
<?php comments_template(); ?>
</div>
</div>
</div>
<?php get_footer(); ?>