<?php if ( is_singular() ) : ?>
<meta property="og:type" content="article" />
<?php else: ?>
<meta property="og:type" content="website" />
<?php endif; ?>
<meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
<meta property="og:locale" content="zh_CN" />
<?php if ( is_home() ) : ?>
<meta property="og:title" content="<?php bloginfo('name'); ?>" />
<meta property="og:url" content="<?php bloginfo('url'); ?>/" />
<meta property="og:description" content="<?php echo weisay_option('wei_description'); ?>"/>
<?php $site_icon_url = get_site_icon_url(); if ( !empty( $site_icon_url )) : ?>
<meta property="og:image" content="<?php echo esc_url($site_icon_url); ?>" />
<?php endif; ?>
<?php elseif ( is_category() ) : ?>
<meta property="og:title" content="<?php single_cat_title(); ?>" />
<meta property="og:url" content="<?php
$catID = get_query_var('cat'); 
$thisCat = get_category($catID);
echo get_category_link($thisCat->term_id); ?>" />
<meta property="og:description" content="<?php echo trim($description); ?>" />
<?php elseif ( is_page() ) : ?>
<meta property="og:title" content="<?php the_title(); ?>" />
<meta property="og:url" content="<?php echo get_page_link($page_id); ?>" />
<meta property="og:description" content="<?php echo trim($description); ?>" />
<?php elseif ( is_tag() ) : ?>
<meta property="og:title" content="<?php single_tag_title(); ?>" />
<meta property="og:url" content="<?php echo get_tag_link($tag_id); ?>" />
<meta property="og:description" content="<?php bloginfo('name'); ?>博客上关于<?php single_tag_title(); ?>的所有文章聚合。" />
<?php elseif ( is_year() ) : ?>
<meta property="og:title" content="<?php the_time('Y年'); ?>文章归档" />
<meta property="og:url" content="<?php bloginfo('url'); ?>/<?php the_time('Y'); ?>/" />
<meta property="og:description" content="<?php bloginfo('name'); ?>博客上<?php the_time('Y年'); ?>发布的所有日志聚合。" />
<?php elseif ( is_month() ) : ?>
<meta property="og:title" content="<?php the_time('Y年n月'); ?>文章归档" />
<meta property="og:url" content="<?php bloginfo('url'); ?>/<?php the_time('Y'); ?>/<?php the_time('n'); ?>/" />
<meta property="og:description" content="<?php bloginfo('name'); ?>博客上<?php the_time('Y年n月'); ?>发布的所有日志聚合。" />
<?php elseif ( is_day() ) : ?>
<meta property="og:title" content="<?php the_time('Y年n月j日'); ?>文章归档" />
<meta property="og:url" content="<?php bloginfo('url'); ?>/<?php the_time('Y'); ?>/<?php the_time('n'); ?>/<?php the_time('j'); ?>/" />
<meta property="og:description" content="<?php bloginfo('name'); ?>博客上<?php the_time('Y年n月j日'); ?>发布的所有日志聚合。" />
<?php elseif ( is_author() ) : ?>
<meta property="og:title" content="<?php the_author(); ?>发布的所有文章" />
<meta property="og:url" content="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" />
<meta property="og:description" content="<?php the_author(); ?>在<?php bloginfo('name'); ?>博客上发布的所有日志聚合。" />
<?php elseif ( is_404() || is_search() ) : ?>
<meta property="og:title" content="<?php bloginfo('name'); ?>" />
<?php else: ?>
<meta property="og:title" content="<?php the_title(); ?>" />
<meta property="og:url" content="<?php the_permalink() ?>" />
<?php if (isset($description)) { ?>
<meta property="og:description" content="<?php echo trim($description); ?>" />
<?php } ?>
<?php endif; ?>
<?php if ( is_single() ) { ?>
<?php if (has_post_thumbnail()) : ?>
<meta property="og:image" content="<?php echo get_the_post_thumbnail_url(get_the_ID(),'thumbnail'); ?>" />
<?php elseif ( get_post_meta($post->ID, 'thumbnail', true) ) : ?>
<?php $image = get_post_meta($post->ID, 'thumbnail', true); ?>
<meta property="og:image" content="<?php echo $image; ?>" />
<?php elseif (function_exists('catch_first_image') && ($first_image = catch_first_image())) : ?>
<meta property="og:image" content="<?php echo esc_url($first_image); ?>" />
<?php endif; ?>
<meta property="article:published_time" content="<?php the_time('c') ?>" />
<meta property="article:modified_time" content="<?php the_modified_time('c') ?>" />
<meta property="article:author" content="<?php echo get_the_author_meta('display_name', $post->post_author); ?>" />
<meta property="article:section" content="<?php
$categories = get_the_category();
$category_names = array();
foreach( $categories as $category ) { $category_names[] = esc_html( $category->name ); }
echo implode( ',', $category_names );
?>" />
<?php } ?>