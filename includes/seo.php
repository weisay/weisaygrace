<?php if ( is_home() ) { ?><title><?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></title><?php } ?>
<?php if ( is_search() ) { ?><title>“<?php echo wp_trim_words(get_search_query(), 20, '...' ); ?>”的搜索结果 - <?php bloginfo('name'); ?></title><?php } ?>
<?php if ( is_single() ) { ?><title><?php the_title(); ?> - <?php bloginfo('name'); ?></title><?php } ?>
<?php if ( is_page() ) { ?><title><?php the_title(); ?> - <?php bloginfo('name'); ?></title><?php } ?>
<?php if ( is_category() ) { ?><title><?php single_cat_title(); ?><?php if ( $paged < 2 ) {} else { echo ('_第'); echo ($paged); echo ('页');}?> - <?php bloginfo('name'); ?></title><?php } ?>
<?php if ( is_year() ) { ?><title><?php the_time('Y年'); ?>文章归档<?php if ( $paged < 2 ) {} else { echo ('_第'); echo ($paged); echo ('页');}?> - <?php bloginfo('name'); ?></title><?php } ?>
<?php if ( is_month() ) { ?><title><?php the_time('Y年n月'); ?>文章归档<?php if ( $paged < 2 ) {} else { echo ('_第'); echo ($paged); echo ('页');}?> - <?php bloginfo('name'); ?></title><?php } ?>
<?php if ( is_day() ) { ?><title><?php the_time('Y年n月j日'); ?>文章归档<?php if ( $paged < 2 ) {} else { echo ('_第'); echo ($paged); echo ('页');}?> - <?php bloginfo('name'); ?></title><?php } ?>
<?php if ( is_404() ) { ?><title>页面未找到 - <?php bloginfo('name'); ?></title><?php } ?>
<?php if (function_exists('is_tag')) { if ( is_tag() ) { ?><title><?php  single_tag_title("", true); ?> - <?php bloginfo('name'); ?></title><?php } ?><?php } ?>
<?php if ( is_author() ) {?><title><?php the_author(); ?>发布的所有文章<?php if ( $paged < 2 ) {} else { echo ('_第'); echo ($paged); echo ('页');}?> - <?php bloginfo('name'); ?></title><?php }?>
<?php
function clean_text($text, $length = null) {
	$text = wp_strip_all_tags($text); // 去掉所有HTML标签
	$text = str_replace(array("\r", "\n"), '', $text); // 先去掉多余换行
	$text = preg_replace('/\s+/', ' ', $text); // 合并连续空白
	$text = trim($text); // 去掉首尾空格
	if ($length) {
		$text = mb_substr($text, 0, $length, 'UTF-8');
	}
	return $text;
}
$description = '';
$keywords = '';
if (is_single()) {
	global $post;
	if ($post->post_excerpt) {
		$description = clean_text($post->post_excerpt);
	} else {
		$content = $post->post_content;
		if (preg_match('/<p>(.*?)<\/p>/is', $content, $matches)) {
			$content = $matches[1];
		}
		$description = clean_text($content, 130);
	}
	$tags = wp_get_post_tags($post->ID);
	$keywords = implode(',', wp_list_pluck($tags, 'name'));
} elseif (is_category()) {
	// 分类的description可以到后台 - 文章 -分类目录，填写各分类的描述
	$cat_desc = clean_text(category_description());
	$cat_name = single_cat_title('', false);
	$description = $cat_desc ? $cat_desc : get_bloginfo('name') . "上[{$cat_name}]分类的所有文章聚合。";
	$keywords = $cat_name;
} elseif (is_tag()) {
	// 标签的description可以到后台 - 文章 - 标签，填写各标签的描述
	$tag_desc = clean_text(tag_description());
	$tag_name = single_tag_title('', false);
	$description = $tag_desc ? $tag_desc : get_bloginfo('name') . "上[{$tag_name}]标签的所有文章聚合。";
	$keywords = $tag_name;
} elseif (is_page()) {
	// 页面的description可以到后台 - 页面 - 编辑 - 摘要，填写页面的摘要
	$page_id = get_queried_object_id();
	$post_obj = get_post($page_id);
	$page_name = get_the_title($page_id);
	$page_desc = clean_text($post_obj->post_excerpt);
	$description = $page_desc ? $page_desc : get_bloginfo('name') . "的{$page_name}页面。";
	$keywords = $page_name;
}
?>
<?php echo "\n"; ?>
<?php if (is_home()) : ?>
<meta name="description" content="<?php echo weisay_option('wei_description'); ?>" />
<meta name="keywords" content="<?php echo weisay_option('wei_keywords'); ?>" />
<?php endif; ?>
<?php if (is_single() || is_category() || (is_tag()) || is_page()) : ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php if (!empty($keywords)) : ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php endif; ?>
<?php endif; ?>
<?php if (is_year()) : ?>
<meta name="description" content="<?php bloginfo('name'); ?>上<?php the_time('Y年'); ?>发布的所有文章聚合。" />
<meta name="keywords" content="<?php the_time('Y年'); ?>" />
<?php endif; ?>
<?php if (is_month()) : ?>
<meta name="description" content="<?php bloginfo('name'); ?>上<?php the_time('Y年n月'); ?>份发布的所有文章聚合。" />
<meta name="keywords" content="<?php the_time('Y年n月'); ?>" />
<?php endif; ?>
<?php if (is_day()) : ?>
<meta name="description" content="<?php bloginfo('name'); ?>上<?php the_time('Y年n月j日'); ?>发布的所有文章聚合。" />
<meta name="keywords" content="<?php the_time('Y年n月j日'); ?>" />
<?php endif; ?>
<?php if (is_author()) : ?>
<meta name="description" content="<?php the_author(); ?>在<?php bloginfo('name'); ?>上发布的所有文章聚合。" />
<meta name="keywords" content="<?php the_author(); ?>" />
<?php endif; ?>