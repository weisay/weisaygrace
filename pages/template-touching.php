<?php
/*
Template Name: 走心评论
*/
?>
<?php get_header(); ?>
<div class="container">
<div class="main main-all">
<div id="comments" class="touching-header">
<?php
global $wpdb;
$karma_mun = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_karma = '1' AND comment_approved = '1'");
$karma_count = number_format_i18n($karma_mun);
if (weisay_option('wei_tcbgimg') == 'hide') : ?>
<div class="touching-title-only">目前已入选 <?php echo $karma_count; ?> 条走心评论</div>
<?php else : ?>
<img class="touching-bg" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tcimg' . rand(1,5) . '.jpg'); ?>" alt="每一条评论，都是一个故事">
<div class="touching-overlay"></div>
<div class="touching-content">
<h2><?php the_title(); ?><sup><?php echo $karma_count; ?>条</sup></h2> 
<p><?php $touching_tagline = weisay_option('wei_tctagline'); echo $touching_tagline ? $touching_tagline : '每一条评论，都是一个故事！'; ?></p>
</div><div class="clear"></div>
<?php endif; ?>
</div>
<script type="text/javascript">
jQuery(function($){const $waterfall=$('.touching-waterfall .touching-list');if(!$waterfall.length)return;function layoutMasonry(){const style=getComputedStyle($waterfall[0],'::before');const cols=parseInt(style.getPropertyValue('--cols'))||4;const gap=parseInt(style.getPropertyValue('--gap'))||16;const colHeights=Array(cols).fill(0);const gridWidth=$waterfall.width();const colWidth=(gridWidth-(cols-1)*gap)/cols;$waterfall.children('.comment').each(function(index){const $item=$(this);$item.css('width',colWidth+'px');let minCol=0;for(let i=1;i<cols;i++){if(colHeights[i]<colHeights[minCol])minCol=i}const top=colHeights[minCol];const left=(colWidth+gap)*minCol;$item.css({top:top+'px',left:left+'px'});colHeights[minCol]+=$item.outerHeight(true)+gap});const maxHeight=Math.max(...colHeights);$waterfall.height(maxHeight);$waterfall.css('opacity',1)}layoutMasonry();$(window).on('resize',function(){clearTimeout(window._rto);window._rto=setTimeout(layoutMasonry,100)})});
</script>
<div class="touching-waterfall">
	<ol class="touching-list">
	<?php
	$comments = get_comments(array(
		'karma' => '1',
		'status' => 'approve',
		'order' => 'desc'
	));
	wp_list_comments(array(
		'max_depth' => -1,
		'type' => 'comment',
		'callback' => 'weisay_touching_comments_list',
		'end-callback' => 'weisay_touching_comments_end_list',
		'per_page' => 24,
		'reverse_top_level' => false
	),$comments);
	?>
	</ol><div class="clear"></div>
	<div class="pagination">
	<?php
	$per_page = 24;
	$page_mun = ceil($karma_mun / $per_page);
	paginate_comments_links(array('total'=> $page_mun));
	?></div>
</div>
</div>
</div>
<?php get_footer(); ?>