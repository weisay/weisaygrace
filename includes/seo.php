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
global $description,$keywords;
$description = '';
$keywords = '';
if ( is_single() ){
	if($post->post_excerpt){
	$description = $post->post_excerpt;
	}else{
	$pc=$post->post_content;
	$st=strip_tags(apply_filters('the_content',$pc));
	$pr=preg_replace("/\s+/",'',$st);
	$description = mb_strimwidth($pr,0,240);
	}
	$tags = wp_get_post_tags($post->ID);
	foreach ($tags as $tag ) {
		$keywords = $keywords . $tag->name . ",";
	}
	$keywords = rtrim($keywords, ', ');
}
elseif (is_category()) {
	// 分类的description可以到后台 - 文章 -分类目录，填写各分类的描述
	$description = category_description();
	$keywords = single_cat_title('', false);
}
elseif (is_tag()){
	// 标签的description可以到后台 - 文章 - 标签，填写各标签的描述
	$description = tag_description();
	$keywords = single_tag_title('', false);
}
$description = trim(strip_tags($description));
$keywords = trim(strip_tags($keywords));
?>
<?php echo "\n"; ?>
<?php if ( is_home() ) { ?>
<meta name="description" content="<?php echo weisay_option('wei_description'); ?>" />
<meta name="keywords" content="<?php echo weisay_option('wei_keywords'); ?>" />
<?php } ?>
<?php if ( is_single() ) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ( is_category() ) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ( is_page() ) { ?>
<meta name="description" content="<?php the_title(); ?>" />
<meta name="keywords" content="<?php the_title(); ?>" />
<?php } ?>
<?php if ( is_tag() ) { ?>
<meta name="description" content="<?php bloginfo('name'); ?>上关于<?php single_tag_title(); ?>的所有文章聚合。" />
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ( is_year() ) { ?>
<meta name="description" content="<?php bloginfo('name'); ?>上<?php the_time('Y年'); ?>发布的所有文章聚合。" />
<meta name="keywords" content="<?php the_time('Y年'); ?>" />
<?php } ?>
<?php if ( is_month() ) { ?>
<meta name="description" content="<?php bloginfo('name'); ?>上<?php the_time('Y年n月'); ?>份发布的所有文章聚合。" />
<meta name="keywords" content="<?php the_time('Y年n月'); ?>" />
<?php } ?>
<?php if ( is_day() ) { ?>
<meta name="description" content="<?php bloginfo('name'); ?>上<?php the_time('Y年n月j日'); ?>发布的所有文章聚合。" />
<meta name="keywords" content="<?php the_time('Y年n月j日'); ?>" />
<?php } ?>
<?php if ( is_author() ) { ?>
<meta name="description" content="<?php the_author(); ?>在<?php bloginfo('name'); ?>上发布的所有文章聚合。" />
<meta name="keywords" content="<?php the_author(); ?>" />
<?php } ?>