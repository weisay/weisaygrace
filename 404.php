<?php get_header(); ?>
<div class="container">
<div class="main">
<div class="crumb">当前位置： <a title="返回首页" href="<?php bloginfo('url'); ?>/">首页</a> &gt; 404 &gt; 页面已飞走，试试搜索吧</div>
<div class="article center article-404">
<img src="<?php bloginfo('template_directory'); ?>/assets/images/404.png" alt="404">
</div>
<div class="article article-phone">
	<h3 class="center article-title">你迷路了?试试搜索吧</h3>
	<div class="search-box">
		<form method="get" class="search-form" action="<?php bloginfo('url'); ?>"/>
			<input type="text" required="" name="s" id="s" value="" class="search-field" placeholder="搜索">
			<button aria-label="搜索" type="submit" class="submit"><i class="iconfont nficon">&#xe652;</i></button>
		</form>
	</div>
</div>
</div>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>