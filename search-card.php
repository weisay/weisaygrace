<?php get_header(); ?>
<div class="container">
<div class="main <?php echo (weisay_option('wei_layout_card_sidebar') == '1') ? 'main-aside' : 'main-all'; ?>" role="main">
<div class="crumb">当前位置： <a title="返回首页" href="<?php echo esc_url( home_url('/') ); ?>">首页</a> &gt; 搜索 &gt; <?php echo wp_trim_words(get_search_query(), 36, '...' ); ?></div>
<div class="post-card <?php echo (weisay_option('wei_layout_card_col') == '4') ? 'card-4' : ((weisay_option('wei_layout_card_col') == '2') ? 'card-2' : 'card-3'); ?>">
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div <?php post_class(); ?> id="post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Article">
<div class="post-thumbnail"><a href="<?php the_permalink() ?>" rel="nofollow" title="<?php the_title(); ?>">
<img class="diagram" src="<?php echo multi_post_thumbnail_url($post->ID, 'medium'); ?>" alt="<?php the_title(); ?>" itemprop="image" loading="lazy" />
</a></div>
<span class="card-category" itemprop="articleSection" content="<?php
$categories = get_the_category();
$category_names = array();
foreach( $categories as $category ) { $category_names[] = esc_html( $category->name ); }
echo implode( ',', $category_names );
?>"><?php the_category(' ') ?></span>
<?php comments_popup_link ('','<span class="card-comments"><span itemprop="interactionCount" content="UserComments:%"><i class="iconfont ccicon">&#xe648;</i>1</span></span>','<span class="card-comments"><span itemprop="interactionCount" content="UserComments:%"><i class="iconfont ccicon">&#xe648;</i>%</span></span>'); ?>
<div class="card-item">
<h2 class="post-title" itemprop="headline"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" itemprop="url"><?php if(is_sticky()) : ?><i class="iconfont topicon">&#xe64e;</i><?php endif; ?><?php the_title(); ?><?php require get_template_directory() . '/includes/new.php'; ?></a></h2>
<?php if (weisay_option('wei_layout_card_excerpt') == '1') : ?>
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
<?php endif; ?>
<div class="post-meta"><span class="vcard author" itemprop="author" itemscope itemtype="https://schema.org/Person"><a itemprop="url" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" style="display:none;"><span itemprop="name"><?php the_author() ?></span></a><span class="fn"><?php the_author() ?></span></span><span class="date" itemprop="datePublished" content="<?php the_time('c') ?>"><?php the_time('Y-m-d') ?></span><?php if(function_exists('the_views')) { echo '<span class="views">'; the_views(); echo '</span>'; } ?></div>
</div>
</div>
<?php endwhile; else: ?>
<div class="article">
<h3 class="article-title search-no">非常抱歉，无法搜索到与之相匹配的信息。</h3>
</div>
<?php endif; ?>
</div>
<?php if(function_exists('paging_nav')) paging_nav(); ?>
</div>
<?php if (weisay_option('wei_layout_card_sidebar') == '1') : ?>
<?php get_sidebar(); ?>
<?php endif; ?>
</div>
<?php get_footer(); ?>