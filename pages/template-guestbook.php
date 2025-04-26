<?php
/*
Template Name: 留言页面
*/
?>
<?php get_header(); ?>
<div class="container">
<div class="main main-all">
<div class="crumb">当前位置： <a title="返回首页" href="<?php bloginfo('url'); ?>/">首页</a> &gt; <h1><?php the_title(); ?></h1></div>
<?php if (weisay_option('wei_hotreviewer') != 'hide') : ?>
<div class="article article-mostactive">
<h3 class="article-title">评论排行 TOP30<span class="article-subtitle">感谢小伙伴们的驻足</a></span></h3>
<div class="top-comment">
<ul>
<?php
global $wpdb;
$counts = wp_cache_get( 'weisay_mostactive' );
$my_email = get_bloginfo ('admin_email');
if ( false === $counts ) {
	$counts = $wpdb->get_results("SELECT COUNT(comment_author) AS cnt, comment_author, comment_author_url, comment_author_email
		FROM {$wpdb->prefix}comments
		WHERE comment_date > date_sub( NOW(), INTERVAL 12 MONTH )
			AND comment_approved = '1'
			AND comment_author_email != '$my_email'
			AND comment_author_url != ''
			AND ( comment_type = '' OR comment_type = 'comment' )
			AND user_id = '0'
		GROUP BY comment_author_email
		ORDER BY cnt DESC
		LIMIT 30");
}
$mostactive = '';
if ( $counts ) {
	wp_cache_set( 'weisay_mostactive', $counts );

	foreach ($counts as $count) {
		$c_url = $count->comment_author_url;
		$mostactive .= '<li>' . '<a href="'. $c_url . '" title="' . $count->comment_author .'" rel="external nofollow">' . get_avatar($count->comment_author_email, 64, '', $count->comment_author . '') . '</a><span class="cnt">'. $count->cnt . '</span></li>'."\n";
	}
	echo $mostactive;
}
?>
<div class="clear"></div>
</ul>
</div>
</div>
<?php endif; ?>
<div class="article">
<?php comments_template(); ?>
</div>
</div>
</div>
<?php get_footer(); ?>