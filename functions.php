<?php
if (!function_exists('optionsframework_init')) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
	require_once dirname( __FILE__ ) . '/inc/options-framework.php';
	$optionsfile = locate_template( 'options.php' );
	load_template( $optionsfile );
}

require get_template_directory() . '/includes/patch.php';
require get_template_directory() . '/includes/patch-emoji.php';
require get_template_directory() . '/includes/theme-updater.php';
require get_template_directory() . '/includes/tags.php'; //标签tag
require get_template_directory() . '/includes/widgets.php'; //小工具
require get_template_directory() . '/com-functions.php'; //评论相关

//IP归属地数据库切换
if (weisay_option('wei_ipv6') == 'open') {
	require get_template_directory() . '/includes/ip2region-full.php';
} else {
	require get_template_directory() . '/includes/ip2region.php';	
}

if (function_exists('register_sidebar'))
{
	register_sidebar(array(
		'name'			=> 'PC端左侧文章目录专用小工具',
		'id' => 'sidebar-0',
		'before_widget'	=> '<div class="fixed-index">',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3 class="widget-title">',
		'after_title'	=> '</h3>',
	));
}
{
	register_sidebar(array(
		'name'			=> 'PC端全局展示的小工具1',
		'id' => 'sidebar-1',
		'before_widget'	=> '<div class="widget">',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3 class="widget-title">',
		'after_title'	=> '</h3>',
	));
}
{
	register_sidebar(array(
		'name'			=> 'PC端只首页展示的小工具',
		'id' => 'sidebar-2',
		'before_widget'	=> '<div class="widget">',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3 class="widget-title">',
		'after_title'	=> '</h3>',
	));
}
{
	register_sidebar(array(
		'name'			=> 'PC端只文章页展示的小工具',
		'id' => 'sidebar-3',
		'before_widget'	=> '<div class="widget">',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3 class="widget-title">',
		'after_title'	=> '</h3>',
	));
}
{
	register_sidebar(array(
		'name'			=> 'PC端全局展示小工具2',
		'id' => 'sidebar-4',
		'before_widget'	=> '<div class="widget">',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3 class="widget-title">',
		'after_title'	=> '</h3>',
	));
}
{
	register_sidebar(array(
		'name'			=> 'PC端固定跟随的小工具（只放一个）',
		'id' => 'sidebar-5',
		'before_widget'	=> '<div class="widget" id="sidebar-follow">',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3 class="widget-title">',
		'after_title'	=> '</h3>',
	));
}
{
	register_sidebar(array(
		'name'			=> '移动端右侧边栏小工具',
		'id' => 'sidebar-6',
		'before_widget'	=> '<div class="widget">',
		'after_widget'	=> '</div>',
		'before_title'	=> '<h3 class="widget-title">',
		'after_title'	=> '</h3>',
	));
}
{
	register_sidebar(array(
		'name'			=> '评论者等级专用小工具',
		'id' => 'sidebar-7',
		'before_widget'	=> '',
		'after_widget'	=> '',
		'before_title'	=> '',
		'after_title'	=> '',
	));
}

if ( function_exists('register_nav_menus') ) {
	register_nav_menus(array(
		'menutop' => 'PC顶部菜单',
		'menunav' => 'PC导航菜单',
		'menuleft' => '移动端左侧菜单',
	));
}

if ( ! function_exists( 'weisaygrace_styles' ) ) {
	function weisaygrace_styles() {
		$theme = wp_get_theme();
		$themeversion = $theme->get('Version');
		wp_enqueue_style( 'weisaygrace-mmenu', get_template_directory_uri().'/assets/css/jquery.mmenu.css','',$themeversion,'all' );
		wp_enqueue_style( 'weisaygrace-style', get_stylesheet_uri(),'',$themeversion,'all' );
		wp_enqueue_style( 'weisaygrace-dark', get_template_directory_uri().'/assets/css/dark.css','',$themeversion,'all' );
	}
}
add_action( 'wp_enqueue_scripts', 'weisaygrace_styles', '1' );

//获取主题版本
function get_weisaygrace_version() {
	static $version = null;
	if (is_null($version)) {
		$theme = wp_get_theme();
		$version = $theme->get('Version');
	}
	return $version;
}

//独立页面增加摘要功能
add_action('init', 'page_excerpt');
function page_excerpt() {
	add_post_type_support('page', array('excerpt'));
}

//添加HTML编辑器自定义快捷按钮
function my_quicktags($mce_settings) {
?>
<?php if (weisay_option('wei_prismjs') == 'open') : ?>
<script type="text/javascript">
QTags.addButton( 'h2', '标题2', "<h2>", "</h2>" );
QTags.addButton( 'h3', '标题3', "<h3>", "</h3>" );
QTags.addButton( 'h4', '标题4', "<h4>", "</h4>" );
QTags.addButton( 'h5', '标题5', "<h5>", "</h5>" );
QTags.addButton( 'h6', '标题6', "<h6>", "</h6>" );
	var aLanguage = ['html', 'css', 'javascript', 'php', 'java', 'c'];
	for( var i = 0, length = aLanguage.length; i < length; i++ ) {
		QTags.addButton(aLanguage[i], aLanguage[i], '<pre class="line-numbers"><code class="language-' + aLanguage[i] + '">\n', '\n</code></pre>');
	}
</script>
<?php endif; ?>
<?php
}
add_action('after_wp_tiny_mce', 'my_quicktags');

//后台评论管理新增地理位置信息
function my_comments_columns( $columns ){
	$columns[ 'location' ] = __( '位置' );
	return $columns;
}
add_filter( 'manage_edit-comments_columns', 'my_comments_columns' );
function output_my_comments_columns(){
	echo convertip(get_comment_author_ip());
}
add_action( 'manage_comments_custom_column', 'output_my_comments_columns', 10, 2 );

//替换文章img和a标签加载fancybox灯箱
add_filter('the_content', 'replace_content');
function replace_content($content) {
	$pattern = '/<a\s([^>]*?)href=([\'"])([^>]*?\.(?:bmp|gif|jpe?g|png|webp)(?:\?[^\'" >]*)?)\2([^>]*?)>(<img\s[^>]*>)<\/a>/is';
	$content = preg_replace_callback($pattern, function($matches) {
		$before_href = $matches[1];
		$href_content = $matches[3];
		$after_href = $matches[4];
		$img_tag = $matches[5];
		$caption = '';
		if (preg_match('/alt=([\'"])(.*?)\1/', $img_tag, $alt_matches)) {
			$alt_value = trim($alt_matches[2]);
			if (!empty($alt_value)) {
				$caption = $alt_value;
			}
		}
		if (empty($caption) && preg_match('/title=([\'"])(.*?)\1/', $img_tag, $title_matches)) {
			$title_value = trim($title_matches[2]);
			if (!empty($title_value)) {
				$caption = $title_value;
			}
		}
		$new_attrs = 'data-fancybox="gallery"';
		if (!empty($caption)) {
			$new_attrs .= ' data-caption="' . esc_attr($caption) . '"';
		}
		return '<a ' . $before_href . 'href="' . $href_content . '"' . $after_href . ' ' . $new_attrs . '>' . $img_tag . '</a>';
	}, $content);
	return $content;
}

//替换Gavatar头像地址
$gravatar_urls = array('www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com', 'cn.gravatar.com');
$gravatar_mirrors = array(
	'weavatar' => 'weavatar.com',
	'cravatar' => 'cravatar.cn',
	'loli' => 'gravatar.loli.net',
	'sep_cc' => 'cdn.sep.cc',
);
if (weisay_option('wei_gravatar') == 'two') {
	$gravatar_mirror = 'cravatar';
} elseif (weisay_option('wei_gravatar') == 'three') {
	$gravatar_mirror = 'loli';
} elseif (weisay_option('wei_gravatar') == 'four') {
	$gravatar_mirror = 'sep_cc';
} else {
	$gravatar_mirror = 'weavatar'; // 选项: weavatar, cravatar, loli, sep_cc
}
function custom_gravatar($avatar) {
	global $gravatar_urls, $gravatar_mirror, $gravatar_mirrors;
	if (isset($gravatar_mirrors[$gravatar_mirror])) {
		return str_replace($gravatar_urls, $gravatar_mirrors[$gravatar_mirror], $avatar);
	}
	return $avatar; // 如果设置的镜像不存在，则原样返回
}
add_filter('get_avatar', 'custom_gravatar');
add_filter('get_avatar_url', 'custom_gravatar');

//搜索关键词为空跳首页
function weisay_redirect_blank_search( $query_variables ) {
	if (isset($_GET['s']) && !is_admin()) {
		if (empty($_GET['s']) || ctype_space($_GET['s'])) {
		wp_redirect( home_url() );
		exit;
		}
	}
return $query_variables;
}
add_filter( 'request', 'weisay_redirect_blank_search' );

//列表页分页
function paging_nav() {
	global $wp_query;
	if ( $wp_query->max_num_pages <= 1 ) {
		return; // 只有一页，不显示分页
	}
	$big = 999999999; // 需要一个不太可能的整数
	$pagination_links = paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => get_option('permalink_structure') ? 'page/%#%/' : '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $wp_query->max_num_pages
	) );
	echo '<div class="pagination">';
	echo $pagination_links;
	echo '</div>';
}

//文章内容分页
function wp_link_pages_ellipsis($args = array()) {
	global $page, $numpages, $multipage, $more;
	if (!$multipage) return;
	$defaults = array(
		'before' => '<div class="fenye">',
		'after' => '</div>',
		'link_before' => '<span>',
		'link_after' => '</span>',
		'echo' => 1,
		'show_all' => false,
		'end_size' => 1,
		'mid_size' => 2,
		'nextpagelink' => '下一页 »',
		'previouspagelink' => '« 上一页'
	);
	$args = wp_parse_args($args, $defaults);
	extract($args, EXTR_SKIP);
	$output = $before;
	// 上一页
	if ($page > 1) {
		$output .= _wp_link_page($page - 1) . $link_before . $previouspagelink . $link_after . '</a>';
	}
	// 页码循环
	for ($i = 1; $i <= $numpages; $i++) {
		if ($i == $page) {
			$output .= '<span class="current">' . $i . '</span>';
		} elseif (
			$i <= $end_size ||
			($i >= $page - $mid_size && $i <= $page + $mid_size) ||
			$i > $numpages - $end_size
		) {
			$output .= _wp_link_page($i) . $link_before . $i . $link_after . '</a>';
		} elseif (
			$i == $end_size + 1 && $i < $page - $mid_size
		) {
			$output .= '<span class="dots">...</span>';
		} elseif (
			$i == $page + $mid_size + 1 && $i < $numpages - $end_size
		) {
			$output .= '<span class="dots">...</span>';
		}
	}
	// 下一页
	if ($page < $numpages) {
		$output .= _wp_link_page($page + 1) . $link_before . $nextpagelink . $link_after . '</a>';
	}
	$output .= $after;
	if ($echo) {
		echo $output;
	} else {
		return $output;
	}
}

//热评日志
function get_hot_reviews($posts_num = 10, $days = 365) {
	global $wpdb;
	$posts_num = absint($posts_num);
	$days = absint($days);
	$cache_key = "hot_reviews_{$days}_{$posts_num}";
	$hot_reviews = get_transient($cache_key);
	if ($hot_reviews === false) {
		$sql = $wpdb->prepare(
			"SELECT ID, post_title, comment_count
			FROM {$wpdb->posts}
			WHERE post_type = 'post'
			AND post_date >= DATE_SUB(NOW(), INTERVAL %d DAY)
			AND post_status = 'publish'
			ORDER BY comment_count DESC
			LIMIT %d",
			$days,
			$posts_num
		);
		$hot_reviews = $wpdb->get_results($sql);
		set_transient($cache_key, $hot_reviews, 2 * HOUR_IN_SECONDS);
	}
	$output = '';
	if (!empty($hot_reviews)) {
		foreach ($hot_reviews as $post) {
			$title_attr = esc_attr($post->post_title . " （{$post->comment_count} 条评论）");
			$output .= sprintf(
				'<li><a href="%s" rel="bookmark" title="%s">%s</a></li>' . "\n",
				esc_url(get_permalink($post->ID)),
				$title_attr,
				esc_html($post->post_title)
			);
		}
	} else {
		$output = '<li>' . esc_html__('暂无热评日志', 'weisaygrace_theme') . '</li>' . "\n";
	}
	return $output;
}

//热门日志
function get_timespan_most_viewed($mode = '', $limit = 10, $days = 500, $display = true) {
	global $wpdb;
	$mode = sanitize_key($mode);
	$limit = absint($limit);
	$days = absint($days);
	$limit_date = gmdate("Y-m-d H:i:s", current_time('timestamp') - ($days * DAY_IN_SECONDS));
	$cache_key = "most_viewed_{$days}_{$limit}";
	$most_viewed = get_transient($cache_key);
	if ($most_viewed === false) {
		$where = ($mode && $mode !== 'both') ? $wpdb->prepare("p.post_type = %s", $mode) : '1=1';
		$sql = $wpdb->prepare(
			"SELECT p.ID, p.post_title, p.post_date, (pm.meta_value+0) AS views
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID
			WHERE p.post_date < %s
			AND p.post_date > %s
			AND {$where}
			AND p.post_status = 'publish'
			AND pm.meta_key = 'views'
			AND p.post_password = ''
			ORDER BY views DESC
			LIMIT %d",
			current_time('mysql'),
			$limit_date,
			$limit
		);
		$most_viewed = $wpdb->get_results($sql);
		set_transient($cache_key, $most_viewed, 2 * HOUR_IN_SECONDS);
	}
	$result_html = '';
	if (!empty($most_viewed)) {
		foreach ($most_viewed as $post) {
			$views = isset($post->views) ? $post->views : 0;
			$formatted_views = number_format($views);
			$title_attr = esc_attr($post->post_title . " （{$formatted_views} 次浏览）");
			$result_html .= sprintf(
				'<li><a href="%s" rel="bookmark" title="%s">%s</a></li>' . "\n",
				esc_url(get_permalink($post->ID)),
				$title_attr,
				esc_html($post->post_title)
			);
		}
	} else {
		$result_html = '<li>' . esc_html__('暂无热门日志', 'weisaygrace_theme') . '</li>' . "\n";
	}
	if ($display) {
		echo $result_html;
	} else {
		return $result_html;
	}
}

//分类热门日志
function get_timespan_most_viewed_category($type, $mode = '', $limit = 10, $days = 2000, $display = true) {
	global $wpdb, $post, $id;
	$category_id = array();
	if (is_category()) {
		$current_category = get_queried_object();
		$category_id = array($current_category->term_id);
	} else {
		if ($type == 'single') {
			$categories = get_the_category($id);
		} else {
			$categories = get_the_category();
		}
		foreach ($categories as $category) {
			array_push($category_id, $category->term_id);
		}
	}
	$limit = absint($limit);
	$days = absint($days);
	$mode = sanitize_key($mode);
	$limit_date = date("Y-m-d H:i:s", current_time('timestamp') - ($days * DAY_IN_SECONDS));
	$category_key = implode('_', $category_id);
	$cache_key = "most_viewed_category_{$category_key}_{$days}_{$limit}";
	$most_viewed_category = get_transient($cache_key);
	if ($most_viewed_category === false) {
		$category_sql = "$wpdb->term_taxonomy.term_id IN (".implode(',', array_map('absint', $category_id)).')';
		$where = (!empty($mode) && $mode != 'both') 
			? $wpdb->prepare("post_type = %s", $mode) 
			: '1=1';
		$sql = $wpdb->prepare(
			"SELECT DISTINCT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_date, (meta_value+0) AS views 
			FROM $wpdb->posts 
			LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID 
			INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) 
			INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) 
			WHERE post_date < %s 
			AND post_date > %s 
			AND $wpdb->term_taxonomy.taxonomy = 'category' 
			AND $category_sql 
			AND $where 
			AND post_status = 'publish' 
			AND meta_key = 'views' 
			AND post_password = '' 
			ORDER BY views DESC 
			LIMIT %d",
			current_time('mysql'),
			$limit_date,
			$limit
		);
		$most_viewed_category = $wpdb->get_results($sql);
		set_transient($cache_key, $most_viewed_category, 2 * HOUR_IN_SECONDS);
	}
	$result_html = '';
	if (!empty($most_viewed_category)) {
		foreach ($most_viewed_category as $post) {
			$views = isset($post->views) ? $post->views : 0;
			$formatted_views = number_format($views);
			$title_attr = esc_attr($post->post_title . " （{$formatted_views} 次浏览）");
			$result_html .= sprintf(
				'<li><a href="%s" rel="bookmark" title="%s">%s</a></li>' . "\n",
				esc_url(get_permalink($post->ID)),
				$title_attr,
				esc_html($post->post_title)
			);
		}
	} else {
		$result_html = '<li>' . esc_html__('暂无热门日志', 'weisaygrace_theme') . '</li>' . "\n";
	}
	if ($display) {
		echo $result_html;
	} else {
		return $result_html;
	}
}

//获取文章第一张图片
function catch_first_image($post_id = null) {
	$post_id = $post_id ?: get_the_ID();
	if (!$post_id) return '';
	$post_content = get_post_field('post_content', $post_id);
	if (empty($post_content)) return '';
	if (preg_match('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $post_content, $matches)) {
		return esc_url($matches[1]);
	}
	return '';
}

//多模式文章缩略图
function multi_post_thumbnail_url($post_id = null, $size = 'thumbnail') {
	global $post;
	$post_id = $post_id ?: $post->ID;
	$mode = weisay_option('wei_thumbnail');
	// two 模式：特色图 > 自定义字段 > 随机图
	if ($mode === 'two') {
		if (has_post_thumbnail($post_id)) {
			$img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
			return $img ? $img[0] : '';
		} elseif ($image = get_post_meta($post_id, 'thumbnail', true)) {
			return esc_url($image);
		} else {
			$rand = rand(1, 30);
			return esc_url(get_template_directory_uri() . '/assets/images/random/' . $rand . '.png');
		}
	}
	// three 模式：特色图 > 自定义字段 > 第一张图 > 随机图
	if ($mode === 'three') {
		if (has_post_thumbnail($post_id)) {
			$img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
			return $img ? $img[0] : '';
		} elseif ($image = get_post_meta($post_id, 'thumbnail', true)) {
			return esc_url($image);
		} elseif ($first_img = catch_first_image($post_id)) {
			return esc_url($first_img);
		} else {
			$rand = rand(1, 30);
			return esc_url(get_template_directory_uri() . '/assets/images/random/' . $rand . '.png');
		}
	}
	// one 默认模式：只用随机图
	$rand = rand(1, 30);
	return esc_url(get_template_directory_uri() . '/assets/images/random/' . $rand . '.png');
}

//文章标签
function get_post_tags($show_count = true) {
	$tags = get_the_tags();
	$out = '';
	if ($tags && count($tags) > 0) {
		$out .= '<div class="article-tag">';
		foreach ($tags as $tag) {
			$tag_link = get_tag_link($tag->term_id);
			$tag_name = esc_html($tag->name);
			$tag_count = $tag->count;
			$out .= '<a class="article-tag-item" href="' . esc_url($tag_link) . '" rel="tag">';
			$out .= '<span><i class="iconfont topicicon">&#xe659;</i></span>' . $tag_name;
			if ($show_count) {
				$out .= '<span class="article-tag-count">' . $tag_count . '</span>';
			}
			$out .= '</a>';
		}
		$out .= '</div>';
	}
	return $out;
}

//日志归档
class article_archive
{
	function get_posts()
	{
		global $wpdb;
		$posts = get_transient('archive_posts');
		if ($posts !== false) {
			return $posts;
		}
		$query = $wpdb->prepare(
			"SELECT DISTINCT ID, post_date, post_date_gmt, comment_count, post_password 
			FROM $wpdb->posts 
			WHERE post_type = %s AND post_status = %s",
			'post',
			'publish'
		);
		$raw_posts = $wpdb->get_results($query, OBJECT);
		$posts = [];
		foreach ($raw_posts as $post) {
			$month_key = mysql2date('Y.m', $post->post_date);
			$posts[$month_key][] = $post;
		}
		set_transient('archive_posts', $posts, 2 * HOUR_IN_SECONDS);
		return $posts;
	}
	function post_list($atts = [])
	{
		global $wp_locale;
		global $article_clean_archive_config;
		$atts = shortcode_atts([
			'usejs' => $article_clean_archive_config['usejs'],
			'monthorder' => $article_clean_archive_config['monthorder'],
			'postorder' => $article_clean_archive_config['postorder'],
			'postcount' => '1',
			'commentcount' => '1',
		], $atts);
		$atts = array_merge(['usejs' => 1, 'monthorder' => 'new', 'postorder' => 'new'], $atts);
		$posts = $this->get_posts();
		('new' == $atts['monthorder']) ? krsort($posts) : ksort($posts);
		foreach ($posts as $key => $month) {
			$sorter = [];
			foreach ($month as $post) {
				$sorter[] = $post->post_date_gmt;
			}
			$sortorder = ('new' == $atts['postorder']) ? SORT_DESC : SORT_ASC;
			array_multisort($sorter, $sortorder, $month);
			$posts[$key] = $month;
		}
		$html = '<div class="car-container';
		if (1 == $atts['usejs']) $html .= ' car-collapse';
		$html .= '">'. "\n";
		if (1 == $atts['usejs']) {
			$html .= '<select id="archive-selector"></select><a href="#" class="car-toggler">展开所有月份</a>'."\n";
		}
		$html .= '<div class="car-list">' . "\n";
		$first_month = true;
		$output_year = '';
		foreach ($posts as $yearmonth => $month_posts) {
			list($year, $month) = explode('.', $yearmonth);
			if ($year != $output_year) {
				global $wpdb;
				$year_count = $wpdb->get_var($wpdb->prepare(
					"SELECT COUNT(*) FROM $wpdb->posts 
					WHERE YEAR(post_date) = %d AND post_type = 'post' AND post_status = 'publish'",
					$year
				));
				$html .= '<div class="car-year-'.$year.'"><h3 class="cy-title"><span class="cy-yeartitle">' . $year . '年</span> <span class="cy-archive-count">（' . $year_count . ' 篇文章）</span></h3>'."\n";
				$html .= '<ul class="car-mon-list">'."\n";
			}
			$first_post = true;
			foreach ($month_posts as $post) {
				if ($first_post) {
					$spchar = $first_month ? '<span class="car-toggle-icon car-minus">-</span>' : '<span class="car-toggle-icon car-plus">+</span>';
					$html .= '<li class="car-pubyear-'.$year.'"><span class="car-yearmonth">'.$spchar.' ' . sprintf( __('%1$s'), $wp_locale->get_month($month) );
					if ('0' != $atts['postcount']) {
						$html .= '<span class="archive-count">（' . count($month_posts) . ' 篇文章）</span>';
					}
					if (!$first_month) {
						$html .= "</span>\n<ul class='car-monthlisting' style='display:none;'>\n";
					} else {
						$html .= "</span>\n<ul class='car-monthlisting'>\n";
					}
					$first_post = false;
					$first_month = false;
				}
				$html .= '<li><span class="car-days">' . mysql2date('d', $post->post_date) . '日</span><a target="_blank" href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a>';
				if (!empty($post->post_password)) {
					$html .= "";
				} elseif ('0' == $post->comment_count) {
					$html .= '<span class="archive-count">（暂无评论）</span>';
				} else {
					$html .= '<span class="archive-count">（' . $post->comment_count . ' 条评论）</span>';
				}
				$html .= "</li>\n";
			}
			$html .= "</ul>\n</li>\n";
			if ($year != $output_year) {
				$html .= "</ul>\n</div><!--.car-year-xxxx-->\n";
				$output_year = $year;
			}
		}
		$html .= "</div><!--.car-list-->\n</div>\n";
		return $html;
	}
	function post_count()
	{
		$num_posts = wp_count_posts('post');
		return number_format_i18n($num_posts->publish);
	}
	function clear_cache($post_id = 0)
	{
		delete_transient('archive_posts');
	}
}
if (!empty($post->post_content)) {
	$all_config = explode(';', $post->post_content);
	$article_clean_archive_config = [];
	foreach ($all_config as $item) {
		$temp = explode('=', $item);
		$article_clean_archive_config[trim($temp[0])] = htmlspecialchars(strip_tags(trim($temp[1])));
	}
} else {
	$article_clean_archive_config = ['usejs' => 1, 'monthorder' => 'new', 'postorder' => 'new'];
}
$article_archive = new article_archive();
add_action('save_post', [$article_archive, 'clear_cache']);
add_action('deleted_post', [$article_archive, 'clear_cache']);
add_action('wp_trash_post', [$article_archive, 'clear_cache']);

//时间轴日志归档
function get_num_posts_by_year($year) {
	static $cache = array();
	$year = absint($year);
	if (isset($cache[$year])) {
		return $cache[$year];
	}
	global $wpdb;
	if (empty($cache)) {
		$results = $wpdb->get_results("
			SELECT YEAR(post_date) as y, COUNT(*) as cnt 
			FROM $wpdb->posts 
			WHERE post_type = 'post' 
			AND post_status = 'publish' 
			GROUP BY y
		");
		foreach ($results as $row) {
			$cache[$row->y] = absint($row->cnt);
		}
	}
	return isset($cache[$year]) ? $cache[$year] : 0;
}
function timeline_paged_link($i, $title = '', $linktype = '') {
	if (empty($title)) {
		$title = "第 {$i} 页";
	}
	$linktext = empty($linktype) ? $i : $linktype;
	return sprintf(
		' <a class="page-numbers" href="%s" title="%s" rel="nofollow">%s</a> ',
		esc_url(get_pagenum_link($i)),
		esc_attr($title),
		esc_html($linktext)
	);
}
function timeline_paged_nav($query, $paged, $p = 2) {
	$html = '';
	$max_page = $query->max_num_pages;
	if ($max_page <= 1) {
		return $html;
	}
	if (empty($paged)) {
		$paged = 1;
	}
	$html .= '<div class="pagination">';
	if ($paged > $p + 1) {
		$html .= timeline_paged_link(1, '首页', '«');
	}
	if ($paged > 1) {
		$html .= timeline_paged_link($paged - 1, '上一页', '‹');
	}
	if ($paged > $p + 2) {
		$html .= '<span class="page-numbers dots">...</span>';
	}
	for ($i = $paged - $p; $i <= $paged + $p; $i++) {
		if ($i > 0 && $i <= $max_page) {
			$html .= ($i == $paged) 
				? "<span class='page-numbers current'>{$i}</span>"
				: timeline_paged_link($i);
		}
	}
	if ($paged < $max_page - $p - 1) {
		$html .= '<span class="page-numbers dots">...</span>';
	}
	if ($paged < $max_page) {
		$html .= timeline_paged_link($paged + 1, '下一页', '›');
	}
	if ($paged < $max_page - $p) {
		$html .= timeline_paged_link($max_page, '尾页', '»');
	}
	$html .= '</div>';
	return $html;
}
function timeline_archive() {
	global $post;
	$html = '';
	$output = '';
	$paged = max(1, get_query_var('paged'));
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => 40, // 每页40篇文章
		'ignore_sticky_posts' => 1,
		'paged' => $paged,
	);
	$the_query = new WP_Query($args);
	if (!$the_query->have_posts()) {
		return '<p class="no-posts">暂无文章</p>';
	}
	$html .= '<div class="timeline-archive">';
	$posts_rebuild = array();
	while ($the_query->have_posts()) : $the_query->the_post();
		$post_year = get_the_time('Y');
		$post_mon = get_the_time('m');
		$image = multi_post_thumbnail_url($post->ID, 'thumbnail');
		$posts_rebuild[$post_year][$post_mon][] = sprintf(
'<li>
	<div class="tl-archive-img">
		<a target="_blank" href="%s" rel="nofollow">
			<img src="%s" alt="%s" title="%s" loading="lazy" />
		</a>
	</div>
	<div class="tl-archive-box">
		<p class="tl-archive-date">%s</p>
		<p class="tl-archive-title">
			<a target="_blank" href="%s">%s</a>
			<span class="tl-archive-commentcount">(%s 条评论)</span>
		</p>
	</div>
</li>',
esc_url(get_permalink()),
esc_url($image),
esc_attr(get_the_title()),
esc_attr(get_the_title()),
get_the_time('Y-m-d'),
esc_url(get_permalink()),
esc_html(get_the_title()),
get_comments_number()
);
	endwhile;
	wp_reset_postdata();
	foreach ($posts_rebuild as $year => $months) {
		$output .= sprintf(
			'<h3 class="tl-archive-year">%s年 <span class="tl-archive-count">（%s 篇文章）</span></h3>',
			esc_html($year),
			get_num_posts_by_year($year)
		);
		$output .= '<ul class="tl-archive-ul">';
		foreach ($months as $month_posts) {
			foreach ($month_posts as $post_html) {
				$output .= $post_html;
			}
		}
		$output .= '</ul>';
	}
	$html .= $output;
	$html .= '</div>';
	$html .= timeline_paged_nav($the_query, $paged, 2);
	return $html;
}

//检查是否使用小工具
function has_any_active_sidebar($sidebars = null) {
	if ($sidebars === null) {
		global $wp_registered_sidebars;
		$sidebars = array_keys($wp_registered_sidebars);
	}
	if (is_string($sidebars)) {
		$sidebars = array($sidebars);
	}
	if (empty($sidebars) || !is_array($sidebars)) {
		return false;
	}
	foreach ($sidebars as $sidebar) {
		if (is_active_sidebar($sidebar)) {
			return true;
		}
	}
	return false;
}
function display_global_sidebar_notice($sidebars = null) {
	if (!has_any_active_sidebar($sidebars) && current_user_can('edit_theme_options')) {
		echo '<div class="widget">';
		echo '<h3 class="widget-title">添加小工具</h3>';
		echo '<ul><li>';
		echo '<a href="' . admin_url('widgets.php') . '" target="_blank">为侧边栏添加小工具</a>';
		echo '</li></ul>';
		echo '</div>';
	}
}

//全部设置结束
?>