<nav id="menu">
<dl class="menuside">
<dd class="mm-search">
	<form class="mm-search-form" method="get" action="<?php bloginfo('url'); ?>/">
		<input class="mm-search-input" required="" placeholder="Search" type="text" name="s" autocomplete="off">
	</form>
</dd>
</dl>
<?php wp_nav_menu( array( 'theme_location' => 'menuleft' ) ); ?>
<?php if ( is_user_logged_in() ) : ?>
<section class="mm-wp-admin">
	<span><a href="<?php bloginfo('url') ?>/wp-admin/">控制面板</a></span>
	<span><a href="<?php bloginfo('url') ?>/wp-login.php?action=logout&amp;redirect_to=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>">注销</a></span>
</section>
<?php endif; ?>
</nav>