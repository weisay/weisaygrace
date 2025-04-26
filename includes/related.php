<div class="article-related">
<h3 class="article-title">您可能还会对这些文章感兴趣！</h3>
<ul>
	<?php
	$post_num = 8; 
	global $post;
	$exists_related_ids = array();
	$tmp_post = $post;
	$tags = ''; $i = 0;
	$exists_related_ids[] = $post->ID;
	if ( get_the_tags( $post->ID ) ) {
	foreach ( get_the_tags( $post->ID ) as $tag ) $tags .= $tag->slug . ',';
	$tags = strtr(rtrim($tags, ','), ' ', '-');
	$myposts = get_posts('numberposts='.$post_num.'&orderby=comment_count&tag='.$tags.'&exclude='.$post->ID);
	foreach($myposts as $post) {
	setup_postdata($post);
	?>
	<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?><sup><?php comments_number(' ','(1)','(%)'); ?></sup></a></li>
	<?php
	$exists_related_ids[] = $post->ID;
	$i += 1;
	}
	}
	if ( $i < $post_num ) {
	$post = $tmp_post; setup_postdata($post);
	$cats = ''; 
	$post_num -= $i;
	foreach ( get_the_category( $post->ID ) as $cat ) $cats .= $cat->cat_ID . ',';
	$cats = strtr(rtrim($cats, ','), ' ', '-');
	$myposts = get_posts('numberposts='.$post_num.'&orderby=rand&category='.$cats.'&exclude='. implode(",", $exists_related_ids));
	foreach($myposts as $post) {
	setup_postdata($post);
	?>
	<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?><sup><?php comments_number(' ','(1)','(%)'); ?></sup></a></li>
	<?php
	}
	}
	$post = $tmp_post; setup_postdata($post);
	?>
<div class="clear"></div>
</ul>
</div>