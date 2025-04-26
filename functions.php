<?php
if (!function_exists('optionsframework_init')) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/' );
	require_once dirname( __FILE__ ) . '/inc/options-framework.php';
	$optionsfile = locate_template( 'options.php' );
	load_template( $optionsfile );
}

include("includes/patch.php");
include("includes/patch_emoji.php");
include("includes/iplocation.php");
require get_template_directory() . '/includes/widgets.php';

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
	if( !empty(get_comment_author_ip()) ){
		echo convertip(get_comment_author_ip());
	} else
	{
		echo '火星';
	}
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

//评论者网站新窗口打开
add_filter('get_comment_author_link', function ($return, $author, $id) {
	return str_replace('<a ', '<a target="_blank" ', $return);
},0,3 );

//替换Gavatar头像地址
$gravatar_urls = array('www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com', 'cn.gravatar.com');
function weavatar($avatar)
{
	global $gravatar_urls;
	return str_replace($gravatar_urls, 'weavatar.com', $avatar);
}
function cravatar($avatar)
{
	global $gravatar_urls;
	return str_replace($gravatar_urls, 'cravatar.cn', $avatar);
}
function loli_avatar($avatar)
{
	global $gravatar_urls;
	return str_replace($gravatar_urls, 'gravatar.loli.net', $avatar);
}
function sep_cc_avatar($avatar)
{
	global $gravatar_urls;
	return str_replace($gravatar_urls, 'cdn.sep.cc', $avatar);
}
if (weisay_option('wei_gravatar') == 'two') {
	add_filter('get_avatar', 'cravatar');
	add_filter('get_avatar_url', 'cravatar');
} elseif (weisay_option('wei_gravatar') == 'three') {
	add_filter('get_avatar', 'loli_avatar');
	add_filter('get_avatar_url', 'loli_avatar');
} elseif (weisay_option('wei_gravatar') == 'four') {
	add_filter('get_avatar', 'sep_cc_avatar');
	add_filter('get_avatar_url', 'sep_cc_avatar');
} else {
	add_filter('get_avatar', 'weavatar');
	add_filter('get_avatar_url', 'weavatar');
}

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

//分页
function paging_nav(){
	global $wp_query;
	$big = 999999999; // 需要一个不太可能的整数
	$pagination_links = paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $wp_query->max_num_pages
	) );
	echo '<div class="pagination">';
	echo $pagination_links;
	echo '</div>';
}

// 获得热评文章
function simple_get_most_viewed($posts_num=10, $days=365){
	global $wpdb;
	$sql = "SELECT ID , post_title , comment_count
			FROM $wpdb->posts
			WHERE post_type = 'post' AND TO_DAYS(now()) - TO_DAYS(post_date) < $days
			AND ($wpdb->posts.`post_status` = 'publish' OR $wpdb->posts.`post_status` = 'inherit')
			ORDER BY comment_count DESC LIMIT 0 , $posts_num ";
	$posts = $wpdb->get_results($sql);
	$output = "";
	foreach ($posts as $post){
		$output .= "\n<li><a href= \"".get_permalink($post->ID)."\" rel=\"bookmark\" title=\"".$post->post_title." (".$post->comment_count."条评论)\" >". $post->post_title."</a></li>";
	}
	echo $output;
}

//热门日志
function get_timespan_most_viewed($mode = '', $limit = 10, $days = 500, $display = true) {
	global $wpdb, $post;
	$limit_date = current_time('timestamp') - ($days*86400);
	$limit_date = date("Y-m-d H:i:s",$limit_date);
	$where = '';
	$temp = '';
	if(!empty($mode) && $mode != 'both') {
		$where = "post_type = '$mode'";
	} else {
		$where = '1=1';
	}
	$most_viewed = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_date < '".current_time('mysql')."' AND post_date > '".$limit_date."' AND $where AND post_status = 'publish' AND meta_key = 'views' AND post_password = '' ORDER  BY views DESC LIMIT $limit");
	if($most_viewed) {
	foreach ($most_viewed as $post) {
		$post_title = get_the_title();
		$post_views = intval($post->views);
		$post_views = number_format($post_views);
		$temp .= "<li><a href=\"".get_permalink()."\" title=\"".get_the_title()."\">$post_title</a></li>\n";
	}
	} else {
		$temp = '<li>'.__('暂无热门日志', 'wp-postviews').'</li>'."\n";
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}

//分类热门日志
function get_timespan_most_viewed_category($type, $mode = '', $limit = 10, $days = 2000, $display = true) {
	global $wpdb, $post, $id;
	$categories = null;
	if ($type == 'single') {
		$categories = get_the_category($id);
	} else {
		$categories = get_the_category();
	}
	$category_id = array();
	foreach ($categories as $category) {
		array_push($category_id, $category->term_id);
	}
	$limit_date = current_time('timestamp') - ($days*86400);
	$limit_date = date("Y-m-d H:i:s",$limit_date);
	$where = '';
	$temp = '';
	if(is_array($category_id)) {
		$category_sql = "$wpdb->term_taxonomy.term_id IN (".join(',', $category_id).')';
	} else {
		$category_sql = "$wpdb->term_taxonomy.term_id = $category_id";
	}
	if(!empty($mode) && $mode != 'both') {
		$where = "post_type = '$mode'";
	} else {
		$where = '1=1';
	}
	$most_viewed = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) WHERE post_date < '".current_time('mysql')."' AND post_date > '".$limit_date."' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $category_sql AND $where AND post_status = 'publish' AND meta_key = 'views' AND post_password = '' ORDER BY views DESC LIMIT $limit");
	if($most_viewed) {
		foreach ($most_viewed as $post) {
			$post_title = get_the_title();
			$post_views = intval($post->views);
			$post_views = number_format($post_views);
			$temp .= "<li><a href=\"".get_permalink()."\" title=\"".get_the_title()."\">$post_title</a></li>\n";
		}
	} else {
		$temp = '<li>'.__('暂无热门日志', 'wp-postviews').'</li>'."\n";
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}

//日志归档
class article_archive
{
	function get_posts()
	{
		global  $wpdb;
		if ( $posts = wp_cache_get( 'posts', 'iarticle-clean-archive' ) )
			return $posts;
		$query = $wpdb->prepare(
			"SELECT DISTINCT ID, post_date, post_date_gmt, comment_count, post_password 
			FROM $wpdb->posts 
			WHERE post_type = %s AND post_status = %s",
			'post',
			'publish'
			);
		$raw_posts = $wpdb->get_results($query, OBJECT);
		$posts = array();

		foreach ($raw_posts as $post) {
			$month_key = mysql2date('Y.m', $post->post_date);
			$posts[$month_key][] = $post;
		}
		wp_cache_set( 'posts', $posts, 'iarticle-clean-archive' );
		return $posts;
	}
	function post_list( $atts = array() )
	{
		global $wp_locale;
		global $article_clean_archive_config;
		$atts = shortcode_atts(array(
			'usejs' => $article_clean_archive_config['usejs'],
			'monthorder' => $article_clean_archive_config['monthorder'],
			'postorder' => $article_clean_archive_config['postorder'],
			'postcount' => '1',
			'commentcount' => '1',
		), $atts);
		$atts=array_merge(array('usejs' => 1, 'monthorder' => 'new', 'postorder' => 'new'),$atts);
		$posts = $this->get_posts();
		( 'new' == $atts['monthorder'] ) ? krsort( $posts ) : ksort( $posts );
		foreach( $posts as $key => $month ) {
			$sorter = array();
			foreach ( $month as $post )
				$sorter[] = $post->post_date_gmt;
			$sortorder = ( 'new' == $atts['postorder'] ) ? SORT_DESC : SORT_ASC;
			array_multisort( $sorter, $sortorder, $month );
			$posts[$key] = $month;
			unset($month);
		}
		$html = '<div class="car-container';
		if ( 1 == $atts['usejs'] ) $html .= ' car-collapse';
		$html .= '">'. "\n";
		if ( 1 == $atts['usejs'] ) $html .= '<select id="archive-selector"></select><a href="#" class="car-toggler">展开所有月份'."</a>\n";
		$html .= '<div class="car-list">' . "\n";
		$first_month = TRUE;
		$output_year = '';
		foreach( $posts as $yearmonth => $posts ) {
			list( $year, $month ) = explode( '.', $yearmonth );
			if ($year != $output_year) {
				global $wpdb;
				$year_count = $wpdb->get_var($wpdb->prepare(
					"SELECT COUNT(*) FROM $wpdb->posts 
					WHERE YEAR(post_date) = %d AND post_type = 'post' AND post_status = 'publish'",
					$year
				));
				$html .= '<div class="car-year-'. $year .'"><h3 class="cy-title"><span class="cy-yeartitle">' . $year . '年</span> <span class="cy-archive-count">（' . $year_count . ' 篇文章）</span></h3>'."\n";
				$html .= '<ul class="car-mon-list">'."\n";
			}
			$first_post = TRUE;
			foreach( $posts as $post ) {
				if ( TRUE == $first_post ) {
					$spchar = $first_month ? '<span class="car-toggle-icon car-minus">-</span>' : '<span class="car-toggle-icon car-plus">+</span>';
					$html .= '<li class="car-pubyear-'. $year .'"><span class="car-yearmonth">'.$spchar.' ' . sprintf( __('%1$s'), $wp_locale->get_month($month) );
					if ( '0' != $atts['postcount'] )
					{
						$html .= '<span class="archive-count">（' . count($posts) . ' 篇文章）</span>';
					}
					if ($first_month == FALSE) {
					$html .= "</span>\n<ul class='car-monthlisting' style='display:none;'>\n";
					} else {
					$html .= "</span>\n<ul class='car-monthlisting'>\n";
					}
					$first_post = FALSE;
					$first_month = FALSE;
				}
				$html .= '<li><span class="car-days">' . mysql2date( 'd', $post->post_date ) . '日</span><a target="_blank" href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a>';
				if ( !empty($post->post_password) )
				{
				$html .= "";
				}
				elseif ( '0' == $post->comment_count ){
					$html .= '<span class="archive-count">（暂无评论）</span>';
				}
				elseif ( '0' != $post->comment_count )
				{
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
		$num_posts = wp_count_posts( 'post' );
		return number_format_i18n( $num_posts->publish );
	}
}
if(!empty($post->post_content))
{
	$all_config = explode(';',$post->post_content);
	foreach($all_config as $item)
	{
		$temp = explode('=',$item);
		$article_clean_archive_config[trim($temp[0])] = htmlspecialchars(strip_tags(trim($temp[1])));
	}
}
else
{
	$article_clean_archive_config=array('usejs' => 1, 'monthorder' => 'new', 'postorder' => 'new');
}
$article_archive = new article_archive();

//时间轴日志归档
function get_num_posts_by_year($year) {
	static $cache = array();
	if (isset($cache[$year])) {
		return $cache[$year];
	}
	global $wpdb;
	$count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->posts 
			WHERE YEAR(post_date) = %d 
			AND post_type = 'post' 
			AND post_status = 'publish'",
			$year
		)
	);
	$cache[$year] = $count;
	return $count;
}
function get_timeline_thumbnail($post_id) {
	if (has_post_thumbnail($post_id)) {
		$image_array = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'thumbnail');
		return $image_array[0];
	}
	if ($thumbnail = get_post_meta($post_id, 'thumbnail', true)) {
		return $thumbnail;
	}
	if (function_exists('catch_first_image') && ($first_image = catch_first_image())) {
		return $first_image;
	}
	$random = mt_rand(1, 30);
	return get_bloginfo('stylesheet_directory') . '/assets/images/random/'.$random.'.jpg';
}
function timeline_paged_link($i, $title = '', $linktype = '') {
	if (empty($title)) {
		$title = "第 {$i} 页";
	}
	$linktext = empty($linktype) ? $i : $linktype;
	return sprintf(
		' <a class="page-numbers" href="%s" title="%s">%s</a> ',
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
		$image = get_timeline_thumbnail($post->ID);
		
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

//支持外链缩略图
if ( function_exists('add_theme_support') )
	add_theme_support('post-thumbnails');
	function catch_first_image() {
		global $post, $posts;
		$first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if(isset($matches [1] [0])){
		$first_img = $matches [1] [0];
		}
	return $first_img;
	}

//评论回复自动添加@评论者
function wei_comment_add_at( $comment_text, $comment = '') {
	if(isset($comment->comment_parent) && $comment->comment_parent > 0) {
		$comment_text = '<a rel="nofollow" class="comment_at" href="#comment-' . $comment->comment_parent . '">@'.get_comment_author( $comment->comment_parent ) . '</a>' . $comment_text;
	}
	return $comment_text;
}
add_filter( 'comment_text' , 'wei_comment_add_at', 20, 2);

//评论
function weisay_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	global $commentcount, $wpdb, $post;
	if(!$commentcount) { //初始化楼层计数器
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post->ID AND ( comment_type = '' OR comment_type = 'comment' ) AND comment_approved = '1' AND !comment_parent");
		$cnt = count($comments);//获取主评论总数量
		$page = get_query_var('cpage');//获取当前评论列表页码
		$cpp=get_option('comments_per_page');//获取每页评论显示数量
		if (ceil($cnt / $cpp) == 1 || ($page > 1 && $page  == ceil($cnt / $cpp))) {
			$commentcount = intval($cnt) + 1;//如果评论只有1页或者是最后一页，初始值为主评论总数
		} else {
			$commentcount = intval($cpp) * intval($page) + 1;
		}
	}
?>
<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
<?php $add_below = 'div-comment'; ?>
<div class="comment-avatar vcard"><?php echo get_avatar( $comment, 48, '', get_comment_author() ); ?></div>
<div class="comment-box">
	<?php if ( $comment->comment_approved == '1' ) : ?>
	<span class="floor"><?php
	if(!$parent_id = $comment->comment_parent){
		switch ($commentcount){
		case 2 :echo "沙发";--$commentcount;break;
		case 3 :echo "板凳";--$commentcount;break;
		case 4 :echo "地板";--$commentcount;break;
		default:printf('%1$s楼', --$commentcount);
		}
	}
	?></span><?php endif; ?>
	<div class="fn comment-name"><?php comment_author_link() ?><?php printf(( $comment->user_id === $post->post_author ) ? '<span class="post-author">博主</span>' : ''); ?>：<?php if(function_exists('wpua_custom_output')) { wpua_custom_output(); } ?><?php edit_comment_link('编辑','&nbsp;&nbsp;',''); ?></div>
	<?php if( (weisay_option('wei_touching') == 'open') && ( $comment->comment_karma == '1' )) : ?><div class="touching-comments-chosen"><a href="<?php echo weisay_option('wei_touchingurl'); ?>" target="_blank"><span>入选走心评论</span></a></div><?php endif; ?>
	<div class="comment-content">
		<?php if ( $comment->comment_approved == '0' ) : ?>
		<p class="comment-approved">您的评论正在等待审核中...</p>
		<?php endif; ?>
		<?php comment_text() ?>
	</div>
	<div class="comment-info">
		<span class="datetime"><?php comment_date('Y-m-d') ?> <?php comment_time() ?> <?php if(current_user_can('level_10')) : ?> 来自<?php if( !empty(get_comment_author_ip()) ){echo convertip(get_comment_author_ip());}else{echo '火星';} ?>
		<?php elseif (weisay_option('wei_ipshow') == 'display'): ?>来自<?php if( !empty(get_comment_author_ip()) ){echo convertipsimple(get_comment_author_ip());}else{echo '火星';} ?>
		<?php endif; ?></span>
		<span class="reply">
		<?php 
		$replyButton = get_comment_reply_link(array_merge( $args, array('reply_text' => '<i class="iconfont replyicon">&#xe609;</i>回复', 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'])));
		$replyButton = str_replace( 'data-belowelement', 'onclick="return addComment.moveForm( \'div-comment-'.get_comment_ID().'\', \''.get_comment_ID().'\', \'respond\', \''.get_the_ID().'\', false, this.getAttribute(\'data-replyto\') )" data-belowelement', $replyButton);
		echo $replyButton;
		?>
		</span>
		<?php if (weisay_option('wei_touching') == 'open' && current_user_can('level_10')) : ?>
		<span class="touching-comments-button"><a class="karma-link" data-karma="<?php echo $comment->comment_karma; ?>" href="<?php echo wp_nonce_url( site_url('/comment-karma'), 'KARMA_NONCE' ); ?>" onclick="return post_karma(<?php comment_ID(); ?>, this.href, this)">
		<?php if ($comment->comment_karma == 0) {
		echo '<i class="iconfont hearticon" title="加入走心">&#xe602;</i>';
		} else {
		echo '<i class="iconfont hearticon" title="取消走心">&#xe601;</i>';
		}
		?></a></span>
		<?php endif; ?>
	</div>
</div>
</div>
<?php
}
function weisay_end_comment() {
	echo '</li>';
}

//走心评论独立页面使用
function weisay_touching_comments_list($comment) {
	$cpage = get_page_of_comment( $comment->comment_ID, $args = array() );
?>
<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php $add_below = 'div-comment'; ?>
	<div class="comment-avatar vcard"><?php echo get_avatar( $comment, 48, '', get_comment_author() ); ?></div>
	<div class="comment-box">
	<div class="fn comment-name"><?php comment_author_link() ?><?php if(current_user_can('level_10')) : ?><span class="comment-area">来自<?php if( !empty(get_comment_author_ip()) ){echo convertip(get_comment_author_ip());}else{echo '火星';} ?></span>
	<?php elseif (weisay_option('wei_ipshow') == 'display'): ?><span class="comment-area">来自<?php if( !empty(get_comment_author_ip()) ){echo convertipsimple(get_comment_author_ip());}else{echo '火星';} ?></span>
	<?php endif; ?></div>
	<div class="comment-content">
	<?php comment_text() ?>
	</div>
	<div class="comment-info">
	<span class="datetime"><?php comment_date('Y-m-d') ?> 评论于&nbsp;&nbsp;•&nbsp;&nbsp;<a href="<?php echo get_comment_link($comment->comment_ID, $cpage); ?>" target="_blank"><?php echo get_the_title($comment->comment_post_ID); ?></a></span>
	</div>
	</div>
</div><div class="clear"></div>
<?php
}
function weisay_touching_comments_end_list() {
	echo '</li>';
}

/**
 * 处理走心评论
 * POST /comment-karma
 * 提交三个参数
 *  comment_karma: 0 或者 1
 *  comment_id: 评论ID
 *  _wpnonce: 避免意外提交
 */
function weisay_touching_comments_karma_request() {
	// Check if we're on the correct url
	global $wp;
	$current_slug = add_query_arg( array(), $wp->request );
	if($current_slug !== 'comment-karma') {
		return false;
	}

	global $wp_query;
	if ($wp_query->is_404) {
		$wp_query->is_404 = false;
	}

	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: application/json; charset=utf-8');

	$result = array(
		'code'=> 403,
		'message'=> 'Login required.'
	);

	if (!is_user_logged_in() || !current_user_can('level_10')) {
		header("HTTP/1.1 403 Forbidden");
		die(json_encode($result));
	}

	if (empty($_SERVER['REQUEST_METHOD']) ||
		strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' ||
		empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
		strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
		$result['message'] = 'Request method not allowed';
		header("HTTP/1.1 403 Forbidden");
		die( json_encode($result) );
	}

	$nonce = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING);
	if ( $nonce===false || ! wp_verify_nonce( $nonce,  'KARMA_NONCE')) {
		$result['message'] = 'Security Check';
		header("HTTP/1.1 403 Forbidden");
		die( json_encode($result) );
	}

	if (empty($_POST['comment_id'])) {
		$result['code'] = 501;
		$result['message'] = 'Incorrect parameter';
		header("HTTP/1.1 500 Internal Server Error");
		die( json_encode($result) );
	}

	$comment_karma = empty( $_POST['comment_karma'] ) ? '0' : filter_input(INPUT_POST, 'comment_karma', FILTER_SANITIZE_NUMBER_INT);
	$comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);
	if ($comment_karma === false ||
		$comment_id === false ||
		!is_numeric($comment_karma) ||
		!is_numeric($comment_id)) {
		$result['code'] = 501;
		$result['message'] = 'Incorrect parameter';
		header("HTTP/1.1 500 Internal Server Error");
		die( json_encode($result) );
	}

	// 更新数据库
	$comment_data = array();
	$comment_data['comment_ID'] = intval($comment_id);
	$comment_data['comment_karma'] = intval($comment_karma);
	
	if (wp_update_comment( $comment_data )) {
		$result['code'] = 200;
		$result['message'] = 'ok';
		header("HTTP/1.1 200 OK");
	} else {
		$result['code'] = 502;
		$result['message'] = 'comment update failed';
		header("HTTP/1.1 500 Internal Server Error");
	}

	exit(json_encode($result));
}

add_action( 'template_redirect', 'weisay_touching_comments_karma_request', 0);

//禁止垃圾评论提交到数据库
function Banfuckspam($comment) {
	if( is_user_logged_in()){ return $comment;}
	if( wp_check_comment_disallowed_list( $comment['comment_author'],$comment['comment_author_email'],$comment['comment_author_url'], $comment['comment_content'], $comment['comment_author_IP'], $comment['comment_agent'] )) {
		header("Content-type: text/html; charset=utf-8");
		err( __('禁止发表评论！') );
	} else {
		return $comment;
	}
}
add_filter('preprocess_comment', 'Banfuckspam');

//评论邮件通知
function comment_mail_notify($comment_id) {
	$admin_email = get_bloginfo ('admin_email'); // $admin_email 可改為你指定的 e-mail.
	$comment = get_comment($comment_id);
	$comment_author_email = trim($comment->comment_author_email);
	$parent_id = $comment->comment_parent ? $comment->comment_parent : '';
	$to = $parent_id ? trim(get_comment($parent_id)->comment_author_email) : '';
	$spam_confirmed = $comment->comment_approved;
	if (($parent_id != '') && ($spam_confirmed != 'spam') && ($to != $admin_email) && ($comment_author_email == $admin_email)) {
		$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])); // e-mail 發出點, no-reply 可改為可用的 e-mail.
		$subject = '✨您在 [' . esc_html(get_option('blogname')) . '] 的评论有了新的回复';
		$message = '
		<table style="font-family:Arial,sans-serif;color:#333;margin:0;padding:0;max-width:820px;margin:0 auto;border-radius:0;" border="0" cellpadding="0" cellspacing="0">
<tbody><tr>
	<td>
		<table style="padding:10px 0 30px;" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody><tr>
				<td style="font-size:20px;text-align:left;vertical-align:middle;">' . esc_html(trim(get_comment($parent_id)->comment_author)) . ', 您好!</td>
			</tr>
		</tbody></table>
	<table style="margin-bottom:20px;" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody><tr>
			<td style="font-size:16px;">您在 [<strong><a style="text-decoration:none;color:#333;font-weight:bold;" href="' . esc_url(get_option('home')) . '" target="_blank">' . esc_html(get_option('blogname')) . '</a></strong>] 文章《<strong><a style="text-decoration:none;color:#333;font-weight:bold;" href="' . esc_url(get_permalink($comment->comment_post_ID)) . '" target="_blank">' . esc_html(get_the_title($comment->comment_post_ID)) . '</a></strong>》 中的评论有了新回复：</td>
		</tr>
	</tbody></table>
		<table style="margin-bottom:20px;" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody><tr>
				<td width="100%">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tbody><tr>
							<td style="padding:10px;border-radius:8px;overflow:hidden;color:#333;background-color:#eef1f4;" >
								<div style="display:flex;align-items:center;justify-content:flex-start;">
									<div style="flex-shrink:0;margin-right:10px;">
										<img style="width:48px;height:48px;border-radius:50%;" alt="' . esc_attr(trim(get_comment($parent_id)->comment_author)) . '" src="' . esc_url(get_avatar_url(get_comment($parent_id)->comment_author_email, array('size' => 64))) . '">
									</div>
									<div>
										<strong style="font-size:16px;">' . esc_html(trim(get_comment($parent_id)->comment_author)) . '</strong>
									</div>
								</div>
								<p style="margin-top:10px;margin-right:80px;line-height:26px;">' . nl2br(esc_html(get_comment($parent_id)->comment_content)) . '</p>
							</td>
						</tr>
					</tbody></table>
				</td>
			</tr>
		</tbody></table>
		<div style="margin-bottom:20px;"></div>
		<table style="margin-bottom:0px;" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody><tr>
				<td width="100%">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tbody><tr>
							<td style="padding:10px;text-align:right;border-radius:8px;overflow:hidden;color:#333;background-color:#fff1f3;" >
								<div style="display:flex;align-items:center;justify-content:flex-end;">
									<div>
										<strong style="font-size:16px;">' . esc_html(trim($comment->comment_author)) . '</strong>
									</div>
									<div style="flex-shrink:0;margin-left:10px;">
									<img style="width:48px;height:48px;border-radius:50%;" alt="' . esc_attr(trim($comment->comment_author)) . '" src="' . esc_url(get_avatar_url($comment->comment_author_email, array('size' => 64))) . '">
									</div>
								</div>
								<p style="margin-top:10px;margin-left:80px;line-height:26px;">' . nl2br(esc_html($comment->comment_content)) . '</p>
							</td>
						</tr>
					</tbody></table>
				</td>
			</tr>
		</tbody></table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody><tr>
				<td style="padding:20px 0 30px;" align="center">
					<a style="display:inline-block;padding:10px 20px;background-color:#ed5565;color:#fff;text-decoration:none;border-radius:5px;text-align:center;font-weight:bold;" href="' . esc_url(get_comment_link($parent_id)) . '" target="_blank">查看完整内容</a>
				</td>
			</tr>
		</tbody></table>
	<table style="background-color:#f8f8f8;" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody><tr>
			<td style="color:#666;text-align:center;font-size:12px;padding:15px 0;" width="100%">
				(此邮件由系统自动发送，请勿回复！)
				<span style="display:block;padding-top:8px;border-bottom:1px solid #ccc"></span>
				<a style="display:inline-block;padding-top:8px;text-decoration:none;color:#333;font-size:14px;" href="' . esc_url(get_option('home')) . '" target="_blank">© ' . esc_html(get_option('blogname')) . '</a>
			</td>
		</tr>
	</tbody></table>
	</td>
</tr></tbody>
</table>';
		$message = convert_smilies($message);
		$from = "From: \"" . esc_html(get_option('blogname')) . "\" <$wp_email>";
		$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
		wp_mail( $to, $subject, $message, $headers );
		//echo 'mail to ', $to, '<br/> ' , $subject, $message; // for testing
	}
}
add_action('comment_post', 'comment_mail_notify');

//评论翻页Ajax
function AjaxCommentsPage(){
	if( isset($_POST['action'])&& $_POST['action'] == 'compageajax'){
			$postid = $_POST['postid'];
			$pageid = $_POST['pageid'];/*$args数组将请求的评论页页码做为参数传入评论分页函数*/
			$post = new stdClass();
			$post->ID = $postid;
			$args = array(
				'current' => $pageid,
				'echo' => true
			);
			$order = 'DESC'; 
			/*处理为倒序输出*/
			if( 'asc' != get_option('comment_order') ){
					$order = 'ASC'; 
			}
	global $wp_query, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity;
	/**
	 * Comment author information fetched from the comment cookies.
	 *
	 * @uses wp_get_current_commenter()
	 */
	$commenter = wp_get_current_commenter();
	/**
	 * The name of the current comment author escaped for use in attributes.
	 */
	$comment_author = $commenter['comment_author']; // Escaped by sanitize_comment_cookies()
	/**
	 * The email address of the current comment author escaped for use in attributes.
	 */
	$comment_author_email = $commenter['comment_author_email'];  // Escaped by sanitize_comment_cookies()
	/**
	 * The url of the current comment author escaped for use in attributes.
	 */
	// $comment_author_url = esc_url($commenter['comment_author_url']);
	/** @todo Use API instead of SELECTs. */
	if ( $user_ID) {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt $order", $post->ID, $user_ID));
	} else if ( empty($comment_author) ) {
		$comments = get_comments( array('post_id' => $post->ID, 'status' => 'approve', 'order' => $order ) );
	} else {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date_gmt $order", $post->ID, wp_specialchars_decode($comment_author,ENT_QUOTES), $comment_author_email ));
	}
	// keep $comments for legacy's sake
	$wp_query->comments = apply_filters( 'comments_array', $comments, $post->ID );
	$comments = &$wp_query->comments;
	$wp_query->comment_count = count($wp_query->comments);
	update_comment_cache($wp_query->comments);

		/*下面的输入写入评论页的#ajaxcomment id所在的层*/
		echo "<ol class=\"comment-list\">";
		echo wp_list_comments('callback=weisay_comment&type=comment&end-callback=weisay_end_comment&max_depth=23&page=' . $pageid, $comments);
		//echo wp_list_comments( array( 'callback' => 'weisay_comment' ) );
		echo "</ol><div class=\"pagination comment-navigation\" id=\"commentpager\">";
		$comment_pages = paginate_comments_links($args);
		echo $comment_pages."</div>";
		die();
	}
}
add_action('template_redirect', 'AjaxCommentsPage');

//全部设置结束
?>
