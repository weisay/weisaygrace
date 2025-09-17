<!DOCTYPE html>
<html <?php language_attributes() ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,maximum-scale=2.0,shrink-to-fit=no" />
<meta name="color-scheme" content="light dark" />
<?php require get_template_directory() . '/includes/seo.php'; ?>
<?php
	if (weisay_option('wei_opengraph') != 'close') {
		require get_template_directory() . '/includes/og.php';
	}
	?>
<link rel="profile" href="http://gmpg.org/xfn/11">
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/jquery.min.js?ver=3.7.1'); ?>"></script>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/jquery.mmenu.min.js?ver=' . get_weisaygrace_version()); ?>"></script>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/dark.min.js?ver=' . get_weisaygrace_version()); ?>"></script>
<?php if ( is_singular() ){ ?>
<?php if (weisay_option('wei_prismjs') == 'open') : ?>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/prism.min.js?ver=' . get_weisaygrace_version()); ?>"></script>
<?php endif; ?>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/com-post-ajax.js?ver=' . get_weisaygrace_version()); ?>"></script>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/realgravatar.min.js?ver=' . get_weisaygrace_version()); ?>"></script>
<?php } ?>
<?php if ( is_single() ) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/jquery.fancybox.min.css?ver=' . get_weisaygrace_version()); ?>" />
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/jquery.fancybox.min.js?ver=' . get_weisaygrace_version()); ?>"></script>
<?php } ?>
<?php wp_head(); ?>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/lazyload.min.js?ver=' . get_weisaygrace_version()); ?>"></script>
<script type="text/javascript" src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/weisay.min.js?ver=' . get_weisaygrace_version()); ?>"></script>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
</head>
<body>
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
	<h1><a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a></h1>
<?php else: ?>
	<a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a>
<?php endif; ?>
	<div class="blogdescription"><?php bloginfo('description'); ?></div>
</div>
</div>
</div>
<div class="headermenu">
<a id="hamburger" onfocus="this.blur()" href="#menu" rel="nofollow"><span></span></a><?php bloginfo('name'); ?><a class="icon-right" href="#menu-right" rel="nofollow"><i class="iconfont righticon"></i></a>
</div>
<div class="navigation">
<div class="container mainmenu">
<?php wp_nav_menu( array( 'theme_location' => 'menunav' ) ); ?>
<?php if (weisay_option('wei_search') != 'hide') : ?>
<div class="search">
<form class="search-form" method="get" action="<?php bloginfo('url'); ?>/">
<input class="search-input" required="" value="" type="text" name="s" placeholder="搜索" />
<button aria-label="搜索" class="search-submit iconfont" type="submit">&#xe652;</button></form>
</div>
<?php endif; ?>
</div>
</div>
<div class="roll">
<div id="dark-mode-toggle-button" onclick="toggleColorScheme()" title="点击切换显示模式" class="roll-dark"><i class="iconfont rollmodeicon"></i></div>
<div title="回到顶部" class="roll-top"><i class="iconfont rollicon">&#xe614;</i></div>
</div>