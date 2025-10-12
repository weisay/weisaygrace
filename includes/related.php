<h3 class="article-title">您可能还会对这些文章感兴趣！</h3>
<ul>
<?php
$post_num = 8;
global $post;
$orig_post = $post;
$exists_related_ids = array($post->ID);
$displayed = 0;

// 输出相关日志
function render_related_simple($pid) {
	$title = get_post_field('post_title', $pid);
	$permalink = get_permalink($pid);
	$cc = absint(get_post_field('comment_count', $pid));
	$ccount = $cc ? '<sup>(' . $cc . ')</sup>' : '';
	return '<li><a href="' . esc_url($permalink) . '" rel="bookmark" title="' . esc_attr($title) . '">' . esc_html($title) . $ccount . '</a></li>';
}

// 获取当前文章的标签
$current_tags = wp_get_post_tags($post->ID, array('fields' => 'ids'));

// 优先使用标签匹配相关文章
if (!empty($current_tags)) {
	$args = array(
		'posts_per_page'  => 50,
		'post__not_in' => $exists_related_ids,
		'tag__in' => $current_tags,
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
		'fields' => 'ids'
	);
	$candidate_ids = get_posts($args);

	if (!empty($candidate_ids)) {
		$candidate_ids = array_unique($candidate_ids);
		update_object_term_cache($candidate_ids, 'post');
		$tmp = array();
		foreach ($candidate_ids as $pid) {
			$tags_of_pid = wp_get_post_tags($pid, array('fields' => 'ids'));
			$common = count(array_intersect($current_tags, $tags_of_pid));
			if ($common > 0) {
				$tmp[] = array(
					'id' => $pid,
					'common_tags' => $common,
					'comment_count' => absint(get_post_field('comment_count', $pid))
				);
			}
		}

		// 按共同标签数降序；若相同则按评论数降序
		usort($tmp, function($a, $b) {
			if ($a['common_tags'] === $b['common_tags']) {
				return $b['comment_count'] - $a['comment_count'];
			}
			return $b['common_tags'] - $a['common_tags'];
		});

		$tmp = array_slice($tmp, 0, $post_num);

		foreach ($tmp as $item) {
			echo render_related_simple($item['id']);
			$exists_related_ids[] = $item['id'];
			$displayed++;
		}
	}
}

// 分类随机补充逻辑
if ($displayed < $post_num) {
	$remaining = $post_num - $displayed;
	$cats = wp_get_post_categories($orig_post->ID, array('fields' => 'ids'));

	if (!empty($cats) && $remaining > 0) {
		$args = array(
			'posts_per_page' => 30,
			'post__not_in' => $exists_related_ids,
			'category__in' => $cats,
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby' => 'comment_count',
			'order' => 'DESC',
			'fields' => 'ids'
		);
		$cat_ids = get_posts($args);

		if (!empty($cat_ids)) {
			shuffle($cat_ids);
			$cat_ids = array_slice($cat_ids, 0, $remaining);
			foreach ($cat_ids as $pid) {
				echo render_related_simple($pid);
				$exists_related_ids[] = $pid;
				$displayed++;
			}
		}
	}
}

// 如果完全没有相关日志
if ($displayed === 0) {
	echo '<li class="no-related">暂无相关推荐</li>';
}

$post = $orig_post;
setup_postdata($post);
?>
<div class="clear"></div>
</ul>