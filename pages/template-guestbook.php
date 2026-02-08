<?php
/*
Template Name: 留言页面
*/
?>
<?php get_header(); ?>
<div class="container">
<div class="main main-all" role="main">
<div class="crumb">当前位置： <a title="返回首页" href="<?php echo esc_url( home_url('/') ); ?>">首页</a> &gt; <h1><?php the_title(); ?></h1></div>
<?php if (weisay_option('wei_hotreviewer') != 'hide') : ?>
<div class="article article-mostactive">
<h3 class="article-title">评论排行 TOP30<span class="article-subtitle">感谢小伙伴们的驻足</span></h3>
<div class="top-comment">
<ul>
<?php
global $wpdb;
$cache_key = 'most_active_reviewers';
$counts = get_transient($cache_key);
if (false === $counts) {
	$my_email = get_bloginfo('admin_email');
	$query = $wpdb->prepare(
		"SELECT COUNT(comment_author) AS cnt, comment_author, comment_author_url, comment_author_email
		FROM {$wpdb->prefix}comments
		WHERE comment_date > date_sub(NOW(), INTERVAL 12 MONTH)
			AND comment_approved = '1'
			AND comment_author_email != %s
			AND comment_author_url != ''
			AND (comment_type = '' OR comment_type = 'comment')
			AND user_id = '0'
		GROUP BY comment_author_email
		ORDER BY cnt DESC
		LIMIT 30",
		$my_email
	);
	$counts = $wpdb->get_results($query);
	if ($counts) {
		set_transient($cache_key, $counts, 2 * HOUR_IN_SECONDS);
	}
}
$mostactive = '';
if ($counts && is_array($counts)) {
	foreach ($counts as $count) {
		$author = esc_attr($count->comment_author);
		$url = esc_url($count->comment_author_url);
		$avatar = get_avatar($count->comment_author_email, 84, '', $author);
		$mostactive .= sprintf(
			'<li><a href="%s" title="%s" rel="external nofollow">%s</a><span class="cnt">%d</span></li>%s',
			$url,
			$author,
			$avatar,
			(int)$count->cnt,
			"\n"
		);
	}
	if (!empty($mostactive)) {
		echo $mostactive;
	}
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