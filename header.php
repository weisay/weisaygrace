<!DOCTYPE html>
<html <?php language_attributes() ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,maximum-scale=2.0,shrink-to-fit=no" />
<meta name="color-scheme" content="light dark" />
<?php require_once get_template_directory() . '/includes/seo.php'; ?>
<?php
	if (weisay_option('wei_opengraph') != 'close') {
		require_once get_template_directory() . '/includes/og.php';
	}
	?>
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php echo weisay_option('wei_headcustom'); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="header">
<div class="top-bar">
<div class="container">
<div class="top-page">
<?php wp_nav_menu(array('theme_location' => 'menutop')); ?></div>
<div class="top-social">
<ul class="social-bookmarks">
<li><a class="rss-icon" href="<?php bloginfo('rss2_url'); ?>" target="_blank" title="欢迎订阅<?php bloginfo('name'); ?>"><i class="iconfont socialicon">&#xe8e7;</i></a></li>
</ul>
</div>
</div>
</div>
<div class="clear"></div>
<div class="website container">
<div class="headline">
<?php if( is_home() ) : ?>
	<h1><a href="<?php echo esc_url( home_url('/') ); ?>"><?php bloginfo('name'); ?></a></h1>
<?php else: ?>
	<a href="<?php echo esc_url( home_url('/') ); ?>"><?php bloginfo('name'); ?></a>
<?php endif; ?>
	<div class="blogdescription"><?php bloginfo('description'); ?></div>
</div>
</div>
</div>
<div class="headermenu">
<a id="hamburger" onfocus="this.blur()" href="#menu" rel="nofollow" aria-label="菜单"><span></span></a><a class="mblogurl" href="<?php echo esc_url( home_url('/') ); ?>"><span class="blogname"><?php bloginfo('name'); ?></span></a><a class="icon-right" href="#menu-right" rel="nofollow" aria-label="侧边栏"><i class="iconfont righticon"></i></a>
</div>
<div class="header-navigation">
<div class="container mainmenu">
<?php wp_nav_menu( array( 'theme_location' => 'menunav' ) ); ?>
<?php if (weisay_option('wei_search') != 'hide') : ?>
<div class="search">
<form class="search-form" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
<input class="search-input" required="" value="" type="text" name="s" placeholder="搜索" />
<button aria-label="搜索" class="search-submit iconfont" type="submit">&#xe652;</button></form>
</div>
<?php endif; ?>
</div>
</div>
<div class="roll">
<div id="dark-mode-toggle-button" onclick="toggleColorScheme()" class="roll-toggle">
<div class="toggle-icon">
<i class="iconfont sunmoon sunicon" title="点击切到浅色模式">&#xe61e;</i>
<i class="iconfont sunmoon moonicon" title="点击切到深色模式">&#xe61d;</i>
</div>
</div>
<div title="回到顶部" class="roll-top"><i class="iconfont rollicon">&#xe61a;</i></div>
</div>