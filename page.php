<?php get_header(); ?>
<div class="container">
<div class="main" role="main">
<div class="crumb">当前位置： <a title="返回首页" href="<?php echo esc_url( home_url('/') ); ?>">首页</a> &gt; <h1><?php the_title(); ?></h1></div>
<div class="article">
<h2 class="article-title"><?php the_title(); ?></h2>
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
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>