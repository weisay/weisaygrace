<?php get_header(); ?>
<div class="container">
<div class="main">
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<?php if(is_sticky()) : ?>
<div <?php post_class(); ?> id="post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Article">
<h2 class="sticky-title" itemprop="headline"><i class="iconfont topicon">&#xe64e;</i><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" itemprop="url"><?php the_title();  ?></a></h2></div>
<?php else : ?>
<div <?php post_class(); ?> id="post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Article">
<?php edit_post_link('编辑', '<span class="edit" style="display:none;">', '</span>'); ?>
<h2 class="post-title" itemprop="headline"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" itemprop="url"><?php the_title(); ?></a><span class="new"><?php require get_template_directory() . '/includes/new.php'; ?></span></h2>
<div class="thumbnail"><a href="<?php the_permalink() ?>" rel="nofollow" title="<?php the_title(); ?>">
<img class="diagram" src="<?php echo multi_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>" itemprop="image" loading="lazy" />
</a></div>
<div class="post-content" itemprop="description"><?php
	if(is_singular()){the_content();}else{
	$pc=$post->post_content;
	$st=strip_tags(apply_filters('the_content',$pc));
	if(has_excerpt())
		the_excerpt();
	elseif(preg_match('/<!--more.*?-->/',$pc))
		the_content('');
	elseif(function_exists('mb_strimwidth'))
		echo'<p>'
		.mb_strimwidth($st,0,270,' ...')
		.'</p>';
	else the_content();
}?></div>
<div class="clear"></div>
<div class="post-info"><span class="vcard author info-icon" itemprop="author" itemscope itemtype="https://schema.org/Person"><a itemprop="url" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" style="display:none;"><span itemprop="name"><?php the_author() ?></span></a><span class="fn"><i class="iconfont posticon">&#xe603;</i><?php the_author() ?></span></span><span class="date info-icon" itemprop="datePublished" content="<?php the_time('c') ?>"><i class="iconfont posticon">&#xe689;</i><?php the_time('Y-m-d') ?> <span class="date-hi"><?php the_time('H:i') ?></span></span><span class="category info-icon" itemprop="articleSection" content="<?php
$categories = get_the_category();
$category_names = array();
foreach( $categories as $category ) { $category_names[] = esc_html( $category->name ); }
echo implode( ',', $category_names );
?>"><i class="iconfont posticon">&#xe658;</i><?php the_category(', ') ?></span><?php if(function_exists('the_views')) { echo '<span class="views info-icon"><i class="iconfont posticon">&#xefb8;</i>'; the_views(); echo '</span>'; } ?><span class="comments info-icon"><i class="iconfont posticon">&#xe673;</i><?php comments_popup_link ('抢沙发','1条评论','<span itemprop="interactionCount" content="UserComments:%">%</span>条评论'); ?></span><?php the_tags('<span class="tags" itemprop="keywords"><i class="iconfont posticon">&#xe843;</i>', ', ', '</span>'); ?></div>
<div class="read-more"><a class="read-more-icon" href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow">阅读全文<span></span></a></div>
</div>
<div class="clear"></div>
<?php endif; ?>
<?php endwhile; ?>
<?php endif; ?>
<?php if(function_exists('paging_nav')) paging_nav(); ?>
</div>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>