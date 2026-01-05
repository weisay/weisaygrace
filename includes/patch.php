<?php
// 切换经典小工具
if (weisay_option('wei_widgets') == 'open') {
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );
}

//禁用古腾堡编辑器，对所有文章类型启用经典编辑器
if (weisay_option('wei_editor') == 'open') {
add_filter('use_block_editor_for_post_type', '__return_false', 100);
}

//加载后台友情链接管理
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

//文章编辑页增加特色图片
add_theme_support('post-thumbnails');

//移除头部冗余代码
remove_action( 'wp_head', 'wp_generator' );// WP版本信息
remove_action( 'wp_head', 'rsd_link' );// 离线编辑器接口
remove_action( 'wp_head', 'wlwmanifest_link' );// 同上
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );// 上下文章的url，V5.0+已无
//remove_action( 'wp_head', 'feed_links', 2 );// 文章和评论feed，V5.0+已无
remove_action( 'wp_head', 'feed_links_extra', 3 );// 去除文章评论feed
//remove_action( 'wp_head', 'rel_canonical' ); //去除canonical
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 ); //去除shortlink
remove_action( 'admin_init', '_maybe_update_core'); // 禁止 WordPress 检查更新
remove_action( 'admin_init', '_maybe_update_plugins'); // 禁止 WordPress 更新插件
remove_action( 'admin_init', '_maybe_update_themes');  // 禁止 WordPress 更新主题
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );//移除wp-json链接的代码
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );//移除wp-json链接的代码

//增加网站及评论Feed
add_theme_support( 'automatic-feed-links' );

if (weisay_option('wei_gutenberg_css') == 'close') {
//移除WP核心的global-styles的输出
add_action( 'wp_loaded', function() {
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
}, 20 );
//移出头部古腾堡编辑器css
function remove_wp_gutenberg_css() {
	wp_dequeue_style( 'wp-img-auto-sizes-contain' );
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' ); // 有时移除外部样式表也有用
}
add_action( 'wp_enqueue_scripts', 'remove_wp_gutenberg_css', 100 );
}

//屏蔽谷歌文字
function coolwp_remove_open_sans_from_wp_core() {
	wp_deregister_style( 'open-sans' );
	wp_register_style( 'open-sans', false );
	wp_enqueue_style('open-sans','');
}
add_action( 'init', 'coolwp_remove_open_sans_from_wp_core' );

//评论表情路径
add_filter('smilies_src','custom_smilies_src',1,10);
function custom_smilies_src ($img_src, $img, $siteurl){
	return esc_url(get_template_directory_uri() . '/assets/images/smilies/' . $img);
}

//禁用embeds链接嵌入
function disable_embeds_init() {
global $wp;
$wp->public_query_vars = array_diff( $wp->public_query_vars, array( 'embed', ) );
remove_action( 'rest_api_init', 'wp_oembed_register_route' );//V5.0+已无
add_filter( 'embed_oembed_discover', '__return_false' );
remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );//V5.0+已无
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );//V5.0+已无
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );
add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' ); }
add_action( 'init', 'disable_embeds_init', 9999 );
function disable_embeds_tiny_mce_plugin( $plugins ) { return array_diff( $plugins, array( 'wpembed' ) ); }
function disable_embeds_rewrites( $rules ) { foreach ( $rules as $rule => $rewrite ) { if ( false !== strpos( $rewrite, 'embed=true' ) ) { unset( $rules[ $rule ] ); } }
return $rules; }
function disable_embeds_remove_rewrite_rules() { add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' ); flush_rewrite_rules(); }
register_activation_hook( __FILE__, 'disable_embeds_remove_rewrite_rules' );
function disable_embeds_flush_rewrite_rules() { remove_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' ); flush_rewrite_rules(); }
register_deactivation_hook( __FILE__, 'disable_embeds_flush_rewrite_rules' );

function remove_footer_admin () {
echo '<span id="footer-thankyou">感谢使用<a href="https://wordpress.org/">WordPress</a>和<a href="https://www.weisay.com/blog/wordpress-theme-weisay-grace.html?theme" target="_blank">WeisayGrace主题</a>进行创作。</span>';
}
add_filter('admin_footer_text', 'remove_footer_admin');
?>