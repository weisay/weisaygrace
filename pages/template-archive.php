<?php
/*
Template Name: 归档页面
*/
?>
<?php get_header(); ?>
<?php $theme = wp_get_theme(); $themeversion = $theme -> get('Version'); ?>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/archives.min.js?ver=' . $themeversion); ?>"></script>
<div class="container">
<div class="main">
<div class="crumb">当前位置： <a title="返回首页" href="<?php echo home_url('/'); ?>">首页</a> &gt; <h1><?php the_title(); ?></h1></div>
	<div class="article">
	<?php if (weisay_option('wei_statistics') != 'hide') : ?>
	<div class="article-stat">
	<div class="archives-statistics">
	<div class="archives-info">博客统计信息</div>
	<ul>
	<li title="<?php
$start_date = weisay_option('wei_websitedate');
$output = '';
// 仅当日期有效且是过去日期时计算
if (!empty($start_date) && strtotime($start_date) !== false && strtotime($start_date) < time()) {
	$start_year = date('Y', strtotime($start_date));
	$datetime1 = date_create($start_date);
	$datetime2 = date_create(date('Y-m-d'));
	$interval = date_diff($datetime1, $datetime2);
	$diff = $interval->format('%a');
	$i = 0;
	for ($year = $start_year; $year <= date('Y'); $year++) {
		if (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0) {
			$leap_day = date_create("$year-02-29");
			if ($datetime1 <= $leap_day && $datetime2 >= $leap_day) {
				$i++;
			}
		}
	}
	$days = $diff - $i; // 减去闰年天数
	$y = floor($days / 365);
	$d = $days - $y * 365;
	if ($y > 0) $output .= $y . '年';
	if ($d > 0 || $y == 0) $output .= $d . '天';
}
echo $output;
?>">
		<p class="archives-counts"><span class="archives-count"><?php if (!empty($start_date) && strtotime($start_date) !== false && strtotime($start_date) < time()) { echo floor((time()-strtotime($start_date))/86400); } else { echo ''; } ?></span>+</p>
		<p class="archives-title">运行天数</p>
	</li>
	<li>
		<p class="archives-counts"><span class="archives-count"><?php echo intval(wp_count_terms('category')); ?></span>+</p>
		<p class="archives-title">分类</p>
	</li>
	<li>
		<p class="archives-counts"><span class="archives-count"><?php echo intval(wp_count_posts()->publish); ?></span>+</p>
		<p class="archives-title">文章</p>
	</li>
	<li>
		<p class="archives-counts"><span class="archives-count"><?php echo intval(wp_count_posts('page')->publish); ?></span>+</p>
		<p class="archives-title">页面</p>
	</li>
<?php
global $wpdb;
$my_email = get_bloginfo('admin_email');
$comment_stats = get_transient('archives_comment_stats');
if ($comment_stats === false) {
	$visitor_comments = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1' AND comment_author_email != %s",
			$my_email
		)
	);
	$author_comments = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1' AND comment_author_email = %s",
			$my_email
		)
	);
	$total_comments = $wpdb->get_var(
		"SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'"
	);
	$comment_stats = [
		'visitor' => (int) $visitor_comments,
		'author' => (int) $author_comments,
		'total' => (int) $total_comments,
	];
	set_transient('archives_comment_stats', $comment_stats, 6 * HOUR_IN_SECONDS);
}
?>
	<li title="访客<?php echo $comment_stats['visitor']; ?>条，博主<?php echo $comment_stats['author']; ?>条">
		<p class="archives-counts"><span class="archives-count"><?php echo $comment_stats['total']; ?></span>+</p>
		<p class="archives-title">评论</p>
	</li>
	<li>
		<p class="archives-counts"><span class="archives-count"><?php $link_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'"); echo $link_count; ?></span>+</p>
		<p class="archives-title">友链</p>
	</li>
	</ul>
	</div>
	<div class="clear"></div>
	</div>
	<?php endif; ?>
	<div class="archives-content">
	<?php global $article_archive; echo $article_archive->post_list(); ?>
	</div>
	</div>
</div>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>