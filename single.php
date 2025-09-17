<?php get_header(); ?>
<div class="container">
<div class="main">
<div class="crumb"><div class="expand"><span class="close-sidebar" title="隐藏侧边栏" ><i class="iconfont expandicon">&#xe60b;</i></span><span class="show-sidebar" style= "display:none;" title="显示侧边栏"><i class="iconfont expandicon">&#xe606;</i></span></div>当前位置： <a title="返回首页" href="<?php bloginfo('url'); ?>/">首页</a> &gt; <?php the_category(', ') ?> &gt; 正文</div>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div id="post-<?php the_ID(); ?>" class="article" itemscope itemtype="http://schema.org/Article">
<h1 class="post-title" itemprop="headline"><?php the_title(); ?></h1>
<div class="article-info">
<div class="article-infomation">
<span class="vcard author info-icon" itemprop="author" itemscope itemtype="https://schema.org/Person"><a itemprop="url" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" style="display:none;"><span itemprop="name"><?php the_author() ?></span></a><span class="fn"><i class="iconfont posticon">&#xe603;</i><?php the_author() ?></span></span><span class="date info-icon" itemprop="datePublished" content="<?php the_time('c') ?>"><i class="iconfont posticon">&#xe689;</i><?php the_time('Y-m-d') ?><span class="date-hi"><?php the_time(' H:i') ?></span></span><span class="category info-icon" itemprop="articleSection" content="<?php
$categories = get_the_category();
$category_names = array();
foreach( $categories as $category ) { $category_names[] = esc_html( $category->name ); }
echo implode( ',', $category_names );
?>"><i class="iconfont posticon">&#xe658;</i><?php the_category(', ') ?></span><?php if(function_exists('the_views')) { echo '<span class="views info-icon"><i class="iconfont posticon">&#xefb8;</i>'; the_views(); echo '</span>'; } ?><span class="comments info-icon"><i class="iconfont posticon">&#xe673;</i><?php comments_popup_link ('抢沙发','1条评论','<span itemprop="interactionCount" content="UserComments:%">%</span>条评论'); ?></span></div>
</div>
<?php if ( is_active_sidebar( 'sidebar-0' ) ) : ?>
<?php dynamic_sidebar( 'sidebar-0' ); ?>
<?php endif; ?>
<div class="article-content article-index-area" itemprop="articleBody">
<?php the_content('Read more...'); ?>
<?php wp_link_pages(array('before' => '<div class="fenye">分页：', 'after' => '', 'next_or_number' => 'next', 'previouspagelink' => '<span>上一页</span>', 'nextpagelink' => "")); ?>
<?php wp_link_pages(array('before' => '', 'after' => '', 'next_or_number' => 'number', 'link_before' =>'<span>', 'link_after'=>'</span>')); ?>
<?php wp_link_pages(array('before' => '', 'after' => '</div>', 'next_or_number' => 'next', 'previouspagelink' => '', 'nextpagelink' => "<span>下一页</span>")); ?>
</div>
<?php the_tags('<div class="article-tags"><i class="iconfont posticon" style="padding-right:8px;">&#xe843;</i><span itemprop="keywords">', ', ', '</span></div>'); ?>
<div class="clear"></div>
</div>
<?php if (weisay_option('wei_reward') == 'display') : ?>
<div class="article article-shang">
<div class="shang">
	<span class="zanzhu"><a title="赞助本站" href="javascript:;" onfocus="this.blur()">赏</a></span>
</div>
</div>
<div class="shang-bg"></div>
<div class="shang-content" style="display:none;">
<button class="shang-close" title="关闭">×</button><div class="shang-title">打赏支持</div>
	<div class="shang-body">
	<div class="shang-zfb shang-qrcode"><img alt="支付宝打赏" src="<?php echo weisay_option('wei_alipay'); ?>" width="170" height="170"><span>支付宝打赏</span></div>
	<div class="shang-wx shang-qrcode"><img alt="微信打赏" src="<?php echo weisay_option('wei_wxpay'); ?>" width="170" height="170"><span>微信打赏</span></div>
	<div class="clear"></div>
	<p class="shang-tips">扫描二维码，打赏一下作者吧~</p>
</div>
</div>
<?php endif; ?>
<div class="article">
<ul class="pre-nex">
<li><?php previous_post_link('【上一篇】%link') ?></li>
<li><?php next_post_link('【下一篇】%link') ?></li>
</ul>
</div>
<div class="article">
<?php require get_template_directory() . '/includes/related.php'; ?>
</div>
<div class="article">
<?php comments_template(); ?>
</div>
<?php endwhile; else: ?>
<?php endif; ?>
</div>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>