<?php
/*
Template Name: 友链页面
*/
?>
<?php get_header(); ?>
<div class="container">
<div class="main main-all">
<div class="crumb">当前位置： <a title="返回首页" href="<?php bloginfo('url'); ?>/">首页</a> &gt; <?php the_title(); ?></div>
<div class="article article-link">

<div class="link-blogger">
<?php wp_list_bookmarks (
	array (
		'categorize' => '1',	//链接分类：1显示，0不显示
		'category_orderby' => 'id',	//链接分类排序，可用：name、id、slug、count
		'show_name' => '1',	//链接名称：1显示，0不显示
		'show_images' => '1',	//链接图片：1显示，0不显示
		'show_description' => '1',	//链接描述：1显示，0不显示
		'category_before' => '',
		'category_after' => '' ,
		'title_li' => __('Bookmarks'),
		'title_before' => '<h2 class="links-title">',
		'title_after' => '</h2>',
		'before' => '<li class="links-item">',
		'after' => '</li>',
		'between' => '',
		'link_before' => '<span>',
		'link_after' => '</span>',
		'orderby' => 'link_id',	//友情链接排序，可用：link_id、rand、url、name、target、description、owner、rating、updated、rss、length 等
) ); ?>
</div>

</div>
<div class="article">
<?php comments_template(); ?>
</div>
</div>
</div>
<?php get_footer(); ?>