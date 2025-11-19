<?php
//标签索引函数
if (!function_exists('tag_first_char')) {
	function tag_first_char($char) {
		if ($char === '' || $char === null) return '';
		$first = mb_substr($char, 0, 1, 'UTF-8');
		if (preg_match('/^[A-Za-z0-9]$/', $first)) return strtoupper($first);
		$s1 = @iconv('UTF-8', 'GB2312//IGNORE', $first);
		if ($s1 === false || strlen($s1) < 2) return '#';
		$asc = ord($s1[0]) * 256 + ord($s1[1]) - 65536;
		$map = [
			['A', -20319, -20284], ['B', -20283, -19776], ['C', -19775, -19219],
			['D', -19218, -18711], ['E', -18710, -18527], ['F', -18526, -18240],
			['G', -18239, -17923], ['H', -17922, -17418], ['J', -17417, -16475],
			['K', -16474, -16213], ['L', -16212, -15641], ['M', -15640, -15166],
			['N', -15165, -14923], ['O', -14922, -14915], ['P', -14914, -14631],
			['Q', -14630, -14150], ['R', -14149, -14091], ['S', -14090, -13319],
			['T', -13318, -12839], ['W', -12838, -12557], ['X', -12556, -11848],
			['Y', -11847, -11056], ['Z', -11055, -10247]
		];
		foreach ($map as [$index, $min, $max]) {
			if ($asc >= $min && $asc <= $max) return $index;
		}
		return '#';
	}
}

if (!function_exists('tag_pinyin')) {
	function tag_pinyin($str) {
		if ($str === '' || $str === null) return '';
		return tag_first_char(mb_substr($str, 0, 1, 'UTF-8'));
	}
}

if (!function_exists('tag_index_groups')) {
	function tag_index_groups() {
		$cache_key = 'tag_index_groups';
		$groups = get_transient($cache_key);
		if ($groups === false) {
			$tags = get_terms('post_tag', ['orderby'=>'count','hide_empty'=>1]);
			if (empty($tags) || is_wp_error($tags)) return [];
			$groups = [];
			foreach ($tags as $tag) {
				$index = tag_pinyin($tag->name);
				if (!preg_match('/^[A-Z0-9]$/', $index)) $index = '#';
				$groups[$index][] = $tag;
			}
			ksort($groups);
			foreach ($groups as &$tag_list) {
				usort($tag_list, fn($a,$b)=>intval($b->count)-intval($a->count));
			}
			unset($tag_list);
			set_transient($cache_key, $groups, 12*HOUR_IN_SECONDS);
		}
		return $groups;
	}
}

if (!function_exists('tag_groups_html')) {
	function tag_groups_html() {
		$groups = tag_index_groups();
		if (empty($groups)) { echo '<p class="article-title">暂无标签</p>'; return; }
		$indexs = array_merge(range('A','Z'), range('0','9'), ['#']);
		echo "<ul class='tag-index'>\n";
		foreach ($indexs as $l) {
			if ($l === '#' && empty($groups[$l])) continue;
			echo isset($groups[$l]) ? "<li><a href='#" . esc_attr($l) . "'>$l</a></li>\n" : "<li>$l</li>\n";
		}
		echo "</ul>\n";
		echo "<ul class='tag-list'>\n";
		foreach ($indexs as $l) {
			if (!isset($groups[$l])) continue;
			echo "<li id='" . esc_attr($l) . "'><h4 class='tag-name'>$l</h4>\n";
			foreach ($groups[$l] as $tag) {
				echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '">'
					. esc_html($tag->name) . '<sup>' . intval($tag->count) . '</sup></a> ';
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
}

if (!function_exists('tag_post_count')) {
	function tag_post_count($arg, $type='include') {
		$tags = get_tags([$type=>$arg]);
		if (!empty($tags)) return intval(reset($tags)->count);
		return 0;
	}
}

//标签云集函数
function tag_cloud_list() {
	$cache_key = 'tag_cloud';
	$tags = get_transient($cache_key);
	if ($tags === false) {
		$terms = get_terms([
			'taxonomy'   => 'post_tag',
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
		]);
		if (empty($terms) || is_wp_error($terms)) {
			return '<p class="article-title">暂无标签</p>';
		}
		$tags = [];
		foreach ($terms as $tag) {
			$tags[] = [
				'id'    => $tag->term_id,
				'name'  => $tag->name,
				'count' => intval($tag->count),
				'link'  => get_tag_link($tag->term_id),
			];
		}
		set_transient($cache_key, $tags, 12 * HOUR_IN_SECONDS);
	}
	if (empty($tags)) {
		return '<p class="article-title">暂无标签</p>';
	}
	$out = '<div class="tag-cloud">';
	foreach ($tags as $tag) {
		$out .= '<a class="tag-cloud-item" href="' . esc_url($tag['link']) . '" rel="tag">';
		$out .= '<span><i class="iconfont topicicon">&#xe659;</i></span>' . esc_html($tag['name']);
		$out .= '<span class="tag-cloud-count">' . $tag['count'] . '</span>';
		$out .= '</a>';
	}
	$out .= '</div>';
	return $out;
}

//清理缓存
if (!function_exists('tag_clear_cache')) {
	function tag_clear_cache() {
		delete_transient('tag_index_groups');
		delete_transient('tag_cloud');
	}
	add_action('save_post', 'tag_clear_cache');
	add_action('created_post_tag', 'tag_clear_cache');
	add_action('edited_post_tag', 'tag_clear_cache');
	add_action('delete_post_tag', 'tag_clear_cache');
}

?>
