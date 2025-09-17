<?php
/*
Template Name: 走心评论
*/
?>
<?php get_header(); ?>
<div class="container">
<div class="main main-all">
<div class="crumb">当前位置： <a title="返回首页" href="<?php bloginfo('url'); ?>/">首页</a> &gt; <h1><?php the_title(); ?>评论</h1></div>
<div class="main-all touching-comments-picture"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/touching' . rand(1,4) . '.jpg'); ?>" alt="每一条评论，都是一个故事"></div>
<div class="article">
	<div id="comments" class="comments-area">
	<h3 class="article-title">目前已入选 <?php
		global $wpdb;
		$karmamun = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_karma = '1' AND comment_approved = '1'");
		echo number_format_i18n($karmamun);
		?> 条走心评论<span class="article-subtitle">Prowered by <a target="_blank" href="https://www.weisay.com/blog/wordpress-plugin-touching-comments.html">Touching Comments</a></span></h3>
	<div>
		<ol class="comment-list touching-comments-list">
		<?php
		$comments = get_comments(array(
			'karma' => '1',
			'status' => 'approve',
			'order' => 'desc',
		));
		wp_list_comments(array(
			'max_depth' => -1,
			'type' => 'comment',
			'callback' => 'weisay_touching_comments_list',
			'end-callback' => 'weisay_touching_comments_end_list',
			'per_page' => 20,
			'reverse_top_level' => false
		),$comments);
		?>
		</ol>
		<div class="pagination">
		<?php
		$per_page = 20;
		$pagemun = ceil($karmamun / $per_page);
		$max_page = $pagemun;
		paginate_comments_links(array('total'=> $max_page));
		?></div>
	</div>
	</div>
</div>
</div>
</div>
<?php get_footer(); ?>