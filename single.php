<?php get_header(); ?>
<div class="container">
<div class="main" role="main">
<div class="crumb"><div class="expand"><span class="close-sidebar" title="隐藏侧边栏" ><i class="iconfont expandicon">&#xe60b;</i></span><span class="show-sidebar" style= "display:none;" title="显示侧边栏"><i class="iconfont expandicon">&#xe606;</i></span></div>当前位置： <a title="返回首页" href="<?php echo esc_url( home_url('/') ); ?>">首页</a> &gt; <?php the_category(', ') ?> &gt; 正文</div>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div id="post-<?php the_ID(); ?>" class="article" itemscope itemtype="http://schema.org/Article">
<h1 class="post-title" itemprop="headline"><?php the_title(); ?></h1>
<div class="article-info">
<div class="article-infomation">
<span class="vcard author info-icon" itemprop="author" itemscope itemtype="https://schema.org/Person"><a itemprop="url" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" style="display:none;"><span itemprop="name"><?php the_author() ?></span></a><span class="fn"><i class="iconfont posticon">&#xe603;</i><?php the_author() ?></span></span><span class="date info-icon" itemprop="datePublished" content="<?php the_time('c') ?>"><i class="iconfont posticon">&#xe689;</i><?php the_time('Y-m-d') ?> <span class="date-hi"><?php the_time('H:i') ?></span></span><span class="category info-icon" itemprop="articleSection" content="<?php
$categories = get_the_category();
$category_names = array();
foreach( $categories as $category ) { $category_names[] = esc_html( $category->name ); }
echo implode( ',', $category_names );
?>"><i class="iconfont posticon">&#xe658;</i><?php the_category(', ') ?></span><?php if(function_exists('the_views')) { echo '<span class="views info-icon"><i class="iconfont posticon">&#xefb8;</i>'; the_views(); echo '</span>'; } ?><span class="comments info-icon"><i class="iconfont posticon">&#xe673;</i><?php comments_popup_link ('抢沙发','1条评论','<span itemprop="interactionCount" content="UserComments:%">%</span>条评论'); ?></span></div>
<?php if (weisay_option('wei_qrcode') == 'display'): ?>
<div class="qrcode">
<div class="qrcode-scan">
<ul>
<li class="qrcode-scanimg">
<span><i class="iconfont qrcodeicon">&#xe642;</i>扫一扫手机看<i class="iconfont qrcodeicon">&#xe61b;</i></span>
<div class="qrcode-img">
<div id="qr-output"></div>
</div>
<script>
jQuery(function($){$('#qr-output').qrcode({render:"canvas",text:'<?php echo esc_url( add_query_arg( 'qrcode', '', get_permalink() ) ); ?>',width:100,height:100,<?php $qrcodeUrl = weisay_option('wei_qrcodeimg');if ($qrcodeUrl) {echo "src:'" . esc_js(esc_url($qrcodeUrl)) . "',";} ?>});})
</script>
</li>
</ul>
</div>
</div>
<?php endif; ?>
</div>
<?php if ( is_active_sidebar( 'sidebar-0' ) ) : ?>
<?php dynamic_sidebar( 'sidebar-0' ); ?>
<?php endif; ?>
<div class="article-content article-index-area" itemprop="articleBody">
<?php the_content('Read more...'); ?>
<?php wp_link_pages_ellipsis(); ?>
</div>
<?php if (weisay_option('wei_tagshow') == 'hide') : ?>
<?php echo get_post_tags(false); ?>
<?php else : ?>
<?php echo get_post_tags(true); ?>
<?php endif; ?>
<div class="clear"></div>
<?php if (weisay_option('wei_author_info') != 'hide') : ?>
<div class="article-author">
<div class="article-author-item">
<div class="article-author-avatar">
<?php echo get_avatar( get_the_author_meta('user_email'), 60, '', get_the_author() ); ?>
</div>
<div class="article-author-info">
<p><?php the_author_posts_link(); ?>：<?php
$author_desc = trim( get_the_author_meta('description') );
if ( $author_desc !== '' ) {
	echo esc_html( $author_desc );
} else {
	echo esc_html( get_bloginfo('description') );
}
?></p>
</div>
</div>
<?php if (weisay_option('wei_reward') == 'display') : ?>
<div class="shang">
<span class="zanzhu"><a title="赞助本站" href="javascript:;" onfocus="this.blur()">打赏</a></span>
</div>
<div class="shang-bg"></div>
<div class="shang-content" style="display:none;">
<button class="shang-close" title="关闭">×</button><div class="shang-title">打赏作者</div>
<div class="shang-body">
	<div class="shang-zfb shang-qrcode"><img alt="支付宝打赏" src="<?php echo weisay_option('wei_alipay'); ?>" width="170" height="170"><span>支付宝打赏</span></div>
	<div class="shang-wx shang-qrcode"><img alt="微信打赏" src="<?php echo weisay_option('wei_wxpay'); ?>" width="170" height="170"><span>微信打赏</span></div>
</div>
	<p class="shang-tips">扫描二维码，打赏一下作者吧~</p>
</div>
<?php endif; ?>
</div>
<?php endif; ?>
</div>
<div class="article article-navigation">
<?php
$prev_post = get_previous_post();
if ($prev_post) {
	echo '<a class="nav-item nav-prev" href="' . get_permalink($prev_post->ID) . '">' . "\n";
	if (weisay_option('wei_navimg') != 'hide') {
	echo '<div class="nav-item-image"><img src="' . esc_url(multi_post_thumbnail_url($prev_post->ID, 'thumbnail')) . '" alt="' . esc_attr(get_the_title($prev_post->ID)) . '" itemprop="image" loading="lazy" /></div>' . "\n";
	}
	echo '<div class="nav-item-content">';
	echo '<div class="nav-item-label">上一篇</div>';
	echo '<div class="nav-item-title"><p>' . get_the_title($prev_post->ID) . '</p></div>';
	echo '</div>' . "\n";
	echo '</a>' . "\n";
} else {
	echo '<div class="nav-item nav-prev nav-item-empty"><div class="nav-item-content">已是最早的文章了</div></div>' . "\n";
}
$next_post = get_next_post();
if ($next_post) {
	echo '<a class="nav-item nav-next" href="' . get_permalink($next_post->ID) . '">' . "\n";
	echo '<div class="nav-item-content">';
	echo '<div class="nav-item-label">下一篇</div>';
	echo '<div class="nav-item-title"><p>' . get_the_title($next_post->ID) . '</p></div>';
	echo '</div>' . "\n";
	if (weisay_option('wei_navimg') != 'hide') {
	echo '<div class="nav-item-image"><img src="' . multi_post_thumbnail_url($next_post->ID, 'thumbnail') . '" alt="' . get_the_title($next_post->ID) . '" itemprop="image" loading="lazy" /></div>' . "\n";
	}
	echo '</a>' . "\n";
} else {
	echo '<div class="nav-item nav-next nav-item-empty"><div class="nav-item-content">已是最新的文章了</div></div>' . "\n";
}
?>
</div>
<div class="article article-related">
<?php if (weisay_option('wei_related') == 'two') : ?>
<?php require_once get_template_directory() . '/includes/related.php'; ?>
<?php else: ?>
<?php require_once get_template_directory() . '/includes/related-img.php'; ?>
<?php endif; ?>
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