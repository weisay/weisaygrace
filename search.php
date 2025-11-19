<?php get_header(); ?>
<div class="container">
<div class="main">
<div class="crumb">当前位置： <a title="返回首页" href="<?php bloginfo('url'); ?>/">首页</a> &gt; 搜索 &gt; <?php echo wp_trim_words(get_search_query(), 36, '...' ); ?></div>
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div <?php post_class(); ?> id="post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Article">
<?php edit_post_link('编辑', '<span class="edit" style="display:none;">', '</span>'); ?>
<h2 class="post-title" itemprop="headline"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" itemprop="url"><?php the_title(); ?></a><?php require get_template_directory() . '/includes/new.php'; ?></h2>
<div class="thumbnail"><a href="<?php the_permalink() ?>" rel="nofollow" title="<?php the_title(); ?>">
<img class="diagram" src="<?php echo multi_post_thumbnail_url($post->ID, 'thumbnail'); ?>" alt="<?php the_title(); ?>" itemprop="image" loading="lazy" />
</a></div>
<div class="post-content" itemprop="description"><?php
$pc = $post->post_content;
$full_text = strip_tags(apply_filters('the_content', $pc));
if (has_excerpt()) {
	$excerpt = strip_tags(get_the_excerpt());
	echo '<p>' . $excerpt . '</p>';
}
elseif (preg_match('/<!--more.*?-->/',$pc)) {
	$parts = get_extended($pc);
	$more_text = strip_tags(apply_filters('the_content', $parts['main']));
	echo '<p>' . $more_text . '</p>';
}
else {
	echo '<p>' . mb_strimwidth($full_text, 0, 270, ' ...') . '</p>';
}
?></div>
<div class="clear"></div>
<div class="post-info"><span class="vcard author info-icon" itemprop="author" itemscope itemtype="https://schema.org/Person"><a itemprop="url" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" style="display:none;"><span itemprop="name"><?php the_author() ?></span></a><span class="fn"><i class="iconfont posticon">&#xe603;</i><?php the_author() ?></span></span><span class="date info-icon" itemprop="datePublished" content="<?php the_time('c') ?>"><i class="iconfont posticon">&#xe689;</i><?php the_time('Y-m-d') ?></span><span class="category info-icon" itemprop="articleSection" content="<?php
$categories = get_the_category();
$category_names = array();
foreach( $categories as $category ) { $category_names[] = esc_html( $category->name ); }
echo implode( ',', $category_names );
?>"><i class="iconfont posticon">&#xe658;</i><?php the_category(', ') ?></span><?php if(function_exists('the_views')) { echo '<span class="views info-icon"><i class="iconfont posticon">&#xefb8;</i>'; the_views(); echo '</span>'; } ?><span class="comments info-icon"><i class="iconfont posticon">&#xe673;</i><?php comments_popup_link ('抢沙发','1条评论','<span itemprop="interactionCount" content="UserComments:%">%</span>条评论'); ?></span><?php the_tags('<span class="tags" itemprop="keywords"><i class="iconfont posticon">&#xe843;</i>', ', ', '</span>'); ?></div>
<div class="read-more"><a class="read-more-icon" href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow">阅读全文<span></span></a></div>
</div>
<div class="clear"></div>
<?php endwhile; else: ?>
<div class="article">
<h3 class="article-title">非常抱歉，无法搜索到与之相匹配的信息。</h3>
</div>
<?php endif; ?>
<?php if(function_exists('paging_nav')) paging_nav(); ?>
</div>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>