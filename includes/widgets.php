<?php
//最新评论小工具
class Latest_Comments_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'latest_comments',
			__('weisay最新评论', 'weisaygrace_theme'),
			array(
				'description' => __('显示过滤管理员的最新评论列表', 'weisaygrace_theme'),
			)
		);
	}

	public function widget($args, $instance) {
		$title = apply_filters('widget_title', empty($instance['title']) ? __('最新评论', 'weisaygrace_theme') : $instance['title']);
		$number = empty($instance['number']) ? 8 : absint($instance['number']);

		echo $args['before_widget'];
		
		if ($title) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// 获取管理员邮箱对应的评论ID
	global $wpdb;
	$my_email = get_bloginfo ('admin_email');
	$excluded_comment_ids = $wpdb->get_col("
		SELECT comment_ID 
		FROM $wpdb->comments 
		WHERE comment_author_email = '$my_email'
	");

		// 获取最新评论
		$comments = get_comments(array(
			'number' => $number,
			'status' => 'approve',
			'post_status' => 'publish',
			'comment__not_in' => $excluded_comment_ids
		));

if ($comments) {
	echo '<ul class="recent-comments">';
	foreach ($comments as $comment) {
		echo '<li class="comment-item">';
		echo '<section class="widget-comment-top">';
		echo '<span class="widget-comment-date">' . get_comment_date('Y-m-d', $comment->comment_ID) . '</span>';
		echo get_avatar($comment->comment_author_email, 46, '', strip_tags($comment->comment_author));
		echo '<span class="widget-comment-commentator">' . strip_tags($comment->comment_author) . '</span>';
		echo '</section>';
		echo '<section class="widget-comment-content">';
		$comment_excerpt = wp_trim_words($comment->comment_content, 20);
		printf(
			'<p><a href="%s" title="%s">%s</a></p>',
			esc_url(get_comment_link($comment->comment_ID)),
			esc_attr(sprintf(__('发表在 %s'), get_the_title($comment->comment_post_ID))),
			convert_smilies($comment_excerpt)
		);
		echo '</section>';
		echo '</li>';
	}
	echo '</ul>';
} else {
	echo '<p class="no-comments">' . __('暂无评论', 'weisaygrace_theme') . '</p>';
}

		echo $args['after_widget'];
	}

	public function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 8;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('标题', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><strong><?php _e('显示数量', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" value="<?php echo $number; ?>" size="5" min="1" />
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['number'] = absint($new_instance['number']);
		
		return $instance;
	}
}

function register_latest_comments_widgets() {
	register_widget('Latest_Comments_Widget');
}
add_action('widgets_init', 'register_latest_comments_widgets');

//热门日志小工具
class Popular_Posts_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'popular_posts',
			__('weisay热门日志', 'weisaygrace_theme'),
			array('description' => __('需安装 WP-PostViews 插件，可显示热门文章列表，可配置展示数量和天数', 'weisaygrace_theme'))
		);
	}

	// 前端显示
	public function widget($args, $instance) {
		echo $args['before_widget'];
		if (!empty($instance['title'])) {
			echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
		}
		if (function_exists('the_views')) {
			echo '<ul>';
			if (is_single() || is_category()) {
				// 显示分类热门日志
				$days = !empty($instance['category_days']) ? $instance['category_days'] : 2000;
				get_timespan_most_viewed_category('single', 'post', $instance['number'], $days, true);
			} else {
				// 显示全局热门日志
				$days = !empty($instance['global_days']) ? $instance['global_days'] : 500;
				get_timespan_most_viewed('post', $instance['number'], $days, true);
			}
			echo '</ul>';
		} else {
			echo '<ul><li>热门日志功能需安装 <strong><a href="https://wordpress.org/plugins/wp-postviews/" target="_blank">WP-PostViews</a></strong> 插件。</li></ul>';
		}
		echo $args['after_widget'];
	}

	// 后台表单
	public function form($instance) {
		$title = !empty($instance['title']) ? $instance['title'] : __('热门日志', 'weisaygrace_theme');
		$number = !empty($instance['number']) ? absint($instance['number']) : 10;
		$global_days = !empty($instance['global_days']) ? absint($instance['global_days']) : 500;
		$category_days = !empty($instance['category_days']) ? absint($instance['category_days']) : 2000;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('标题', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><strong><?php _e('显示数量', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="5">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('global_days'); ?>"><strong><?php _e('全局热门天数', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo $this->get_field_id('global_days'); ?>" name="<?php echo $this->get_field_name('global_days'); ?>" type="number" step="1" min="1" value="<?php echo $global_days; ?>" size="5">
			<small><?php _e('默认500天，只统计指定天数内发布的文章', 'weisaygrace_theme'); ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('category_days'); ?>"><strong><?php _e('分类热门天数', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo $this->get_field_id('category_days'); ?>" name="<?php echo $this->get_field_name('category_days'); ?>" type="number" step="1" min="1" value="<?php echo $category_days; ?>" size="5">
			<small><?php _e('默认2000天，只统计指定天数内发布的文章', 'weisaygrace_theme'); ?></small>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 10;
		$instance['global_days'] = (!empty($new_instance['global_days'])) ? absint($new_instance['global_days']) : 500;
		$instance['category_days'] = (!empty($new_instance['category_days'])) ? absint($new_instance['category_days']) : 2000;
		return $instance;
	}
}

function register_popular_posts_widget() {
	register_widget('Popular_Posts_Widget');
}
add_action('widgets_init', 'register_popular_posts_widget');

//选项卡日志小工具-显示最新、热评和随机日志的Tab式列表
class Tabbed_Posts_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'tabbed_posts',
			__('weisay选项卡日志', 'weisaygrace_theme'),
			array('description' => __('显示最新、热评和随机日志的Tab式列表', 'weisaygrace_theme'))
		);
	}

	public function widget($args, $instance) {
		// 获取配置值，设置默认值
		$popular_posts_num = !empty($instance['popular_posts_num']) ? $instance['popular_posts_num'] : 10;
		$popular_days = !empty($instance['popular_days']) ? $instance['popular_days'] : 365;

		echo $args['before_widget'];
		?>
		<div class="tab">
			<ul class="tabnav">
				<li><?php _e('最新日志', 'weisaygrace_theme'); ?></li>
				<li class="selected"><?php _e('热评日志', 'weisaygrace_theme'); ?></li>
				<li><?php _e('随机日志', 'weisaygrace_theme'); ?></li>
			</ul>
		</div>
		<div class="clear"></div>
		<div class="tab-content">
			<!-- 最新日志 -->
			<ul>
				<?php 
				$recent_query = new WP_Query(array(
					'posts_per_page' => $popular_posts_num,
					'orderby' => 'date',
					'order' => 'DESC',
					'ignore_sticky_posts' => 1
				));
				if ($recent_query->have_posts()) :
					while ($recent_query->have_posts()) : $recent_query->the_post(); ?>
						<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
					<?php endwhile;
				else :
					echo '<li>'.__('暂无最新日志', 'weisaygrace_theme').'</li>';
				endif;
				wp_reset_postdata();
				?>
			</ul>

			<!-- 热评日志 -->
			<ul class="active">
				<?php 
				if(function_exists('get_hot_reviews')) {
					echo get_hot_reviews($popular_posts_num, $popular_days);
				} else {
					echo '<li>'.__('暂无热评日志', 'weisaygrace_theme').'</li>';
				}
				?>
			</ul>

			<!-- 随机日志 -->
			<ul>
				<?php 
				$random_query = new WP_Query(array(
					'posts_per_page' => $popular_posts_num,
					'orderby' => 'rand',
					'ignore_sticky_posts' => 1
				));
				if ($random_query->have_posts()) :
					while ($random_query->have_posts()) : $random_query->the_post(); ?>
						<li><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
					<?php endwhile;
				else :
					echo '<li>'.__('暂无随机日志', 'weisaygrace_theme').'</li>';
				endif;
				wp_reset_postdata();
				?>
			</ul>
		</div>
		<?php
		echo $args['after_widget'];
	}

	public function form($instance) {
		// 设置默认值
		$popular_posts_num = !empty($instance['popular_posts_num']) ? $instance['popular_posts_num'] : 10;
		$popular_days = !empty($instance['popular_days']) ? $instance['popular_days'] : 365;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('popular_posts_num'); ?>"><strong><?php _e('显示文章数量', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo $this->get_field_id('popular_posts_num'); ?>"name="<?php echo $this->get_field_name('popular_posts_num'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($popular_posts_num); ?>" size="5" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('popular_days'); ?>"><strong><?php _e('热评日志天数范围', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo $this->get_field_id('popular_days'); ?>" name="<?php echo $this->get_field_name('popular_days'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($popular_days); ?>" size="5" />
			<small><?php _e('只统计指定天数内发布文章的评论数量', 'weisaygrace_theme'); ?></small>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['popular_posts_num'] = (!empty($new_instance['popular_posts_num'])) ? absint($new_instance['popular_posts_num']) : 10;
		$instance['popular_days'] = (!empty($new_instance['popular_days'])) ? absint($new_instance['popular_days']) : 365;
		return $instance;
	}
}

function register_tabbed_posts_widget() {
	register_widget('Tabbed_Posts_Widget');
}
add_action('widgets_init', 'register_tabbed_posts_widget');

//友情链接小工具
class Friend_Links_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'friend_links',
			__('weisay友情链接', 'weisaygrace_theme'),
			array('description' => __('显示友情链接列表', 'weisaygrace_theme'))
		);
	}

	public function widget($args, $instance) {
		echo $args['before_widget'];

		$title = !empty($instance['title']) ? $instance['title'] : __('友情链接', 'weisaygrace_theme');
		echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
		
		$category_id = !empty($instance['link_category']) ? $instance['link_category'] : 0;
		
		echo '<div class="widget-links"><ul>';
		wp_list_bookmarks(array(
			'orderby'		=> 'link_id',
			'categorize'	 => 0,
			'show_images'	=> 0,
			'category'	   => $category_id,
			'title_li'	   => ''
		));
		echo '</ul></div><div class="clear"></div>';
		
		echo $args['after_widget'];
	}

	// 后台表单
	public function form($instance) {
		$title = !empty($instance['title']) ? $instance['title'] : __('友情链接', 'weisaygrace_theme');
		$link_category = !empty($instance['link_category']) ? $instance['link_category'] : '';

		// 获取所有链接分类
		$link_categories = get_terms('link_category', array('hide_empty' => false));
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('标题', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_category'); ?>"><strong><?php _e('链接分类', 'weisaygrace_theme'); ?></strong></label>
			<select class="widefat" id="<?php echo $this->get_field_id('link_category'); ?>" name="<?php echo $this->get_field_name('link_category'); ?>">
				<option value=""><?php _e('所有链接', 'weisaygrace_theme'); ?></option>
				<?php foreach ($link_categories as $category) : ?>
					<option value="<?php echo $category->term_id; ?>" 
							<?php selected($link_category, $category->term_id); ?>>
						<?php echo $category->name; ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['link_category'] = (!empty($new_instance['link_category'])) ? $new_instance['link_category'] : '';
		return $instance;
	}
}

function register_friend_links_widget() {
	register_widget('Friend_Links_Widget');
}
add_action('widgets_init', 'register_friend_links_widget');

//彩色标签云小工具
class Colourful_Tag_Cloud_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'colourful_tag_cloud',
			__('weisay彩色标签云', 'weisaygrace_theme'),
			array('description' => __('可配置的彩色标签云', 'weisaygrace_theme'))
		);
	}

	public function form($instance) {
		$instance = wp_parse_args((array)$instance, array(
			'title' => __('标签云', 'weisaygrace_theme'),
			'number' => 26,
			'orderby' => 'name',
			'order' => 'ASC',
			'show_count' => 0
		));
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><strong><?php _e('标题', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><strong><?php _e('显示数量', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" value="<?php echo absint($instance['number']); ?>" min="1" max="100">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>"><strong><?php _e('排序依据', 'weisaygrace_theme'); ?></strong></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('orderby')); ?>" name="<?php echo esc_attr($this->get_field_name('orderby')); ?>">
				<option value="name" <?php selected($instance['orderby'], 'name'); ?>><?php _e('按名称'); ?></option>
				<option value="count" <?php selected($instance['orderby'], 'count'); ?>><?php _e('按使用次数'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('order')); ?>"><strong><?php _e('排序方式', 'weisaygrace_theme'); ?></strong></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('order')); ?>" name="<?php echo esc_attr($this->get_field_name('order')); ?>">
				<option value="ASC" <?php selected($instance['order'], 'ASC'); ?>><?php _e('升序 (A→Z)', 'weisaygrace_theme'); ?></option>
				<option value="DESC" <?php selected($instance['order'], 'DESC'); ?>><?php _e('降序 (Z→A)', 'weisaygrace_theme'); ?></option>
				<option value="RAND" <?php selected($instance['order'], 'RAND'); ?>><?php _e('随机排序', 'weisaygrace_theme'); ?></option>
			</select>
		</p>
		<p>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" name="<?php echo esc_attr($this->get_field_name('show_count')); ?>" value="1" <?php checked($instance['show_count'], 1); ?>>
			<label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>"><strong><?php _e('显示标签计数', 'weisaygrace_theme'); ?></strong></label>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['number'] = absint($new_instance['number']);
		$instance['orderby'] = sanitize_text_field($new_instance['orderby']);
		$instance['order'] = sanitize_text_field($new_instance['order']);
		$instance['show_count'] = !empty($new_instance['show_count']) ? 1 : 0;
		return $instance;
	}

	public function widget($args, $instance) {
		echo $args['before_widget'];

		if (!empty($instance['title'])) {
			echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
		}

		echo '<div class="widget-tags">';
		wp_tag_cloud(array(
			'smallest'  => 14,
			'largest'   => 14,
			'unit'	  => 'px',
			'number'	=> $instance['number'],
			'orderby'   => $instance['orderby'],
			'order'	 => $instance['order'],
			'show_count'=> $instance['show_count'],
			'taxonomy'  => 'post_tag',
			'echo'	  => true
		));
		echo '</div>';

		echo $args['after_widget'];
	}
}

function register_colourful_tag_cloud_widget() {
	register_widget('Colourful_Tag_Cloud_Widget');
}
add_action('widgets_init', 'register_colourful_tag_cloud_widget');

// 博客统计小工具
class Blog_Stats_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'blog_stats',
			__('weisay博客统计', 'weisaygrace_theme'),
			array('description' => __('显示博客的各种统计数据', 'weisaygrace_theme'))
		);
	}

	// 小工具前端显示
	public function widget($args, $instance) {
		global $wpdb;

		// 获取设置
		$title = apply_filters('widget_title', $instance['title']);
		$site_launch_date = isset($instance['site_launch_date']) ? $instance['site_launch_date'] : '2007-04-22';

		// 计算各种统计数据
		$published_posts = intval(wp_count_posts()->publish);
		$approved_comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");
		$visible_links = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'");
		$tags = wp_count_terms('post_tag');
		$days_running = floor((time()-strtotime($site_launch_date))/86400);

		// 输出小工具
		echo $args['before_widget'];

		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo '<ul>';
		// 文章总数 - 总是显示
		echo '<li>文章总数：<em>' . number_format($published_posts) . '</em> 篇</li>';
		
		// 评论总数 - 仅当有评论时显示
		if ($approved_comments > 0) {
			echo '<li>评论总数：<em>' . number_format($approved_comments) . '</em> 条</li>';
		}
		
		// 友情链接 - 仅当有友情链接时显示
		if ($visible_links > 0) {
			echo '<li>友情总数：<em>' . number_format($visible_links) . '</em> 个</li>';
		}
		
		// 标签总数 - 仅当有标签时显示
		if ($tags > 0) {
			echo '<li>标签总数：<em>' . number_format($tags) . '</em> 个</li>';
		}
		
		// 建站日期和天数 - 总是显示
		echo '<li>建站日期：<em>' . $site_launch_date . '</em></li>';
		echo '<li>建站天数：<em>' . number_format($days_running) . '</em> 天</li>';
		echo '</ul>';
		
		echo $args['after_widget'];
	}

	// 小工具后台表单
	public function form($instance) {
		$title = isset($instance['title']) ? $instance['title'] : '博客统计';
		$site_launch_date = isset($instance['site_launch_date']) ? $instance['site_launch_date'] : '2007-04-22';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('标题', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('site_launch_date'); ?>"><strong><?php _e('建站日期 (YYYY-MM-DD)', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('site_launch_date'); ?>" name="<?php echo $this->get_field_name('site_launch_date'); ?>" type="text" value="<?php echo esc_attr($site_launch_date); ?>" />
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['site_launch_date'] = (!empty($new_instance['site_launch_date'])) ? strip_tags($new_instance['site_launch_date']) : '2007-04-22';
		return $instance;
	}
}

function register_blog_stats_widget() {
	register_widget('Blog_Stats_Widget');
}
add_action('widgets_init', 'register_blog_stats_widget');


//关于博主小工具
class About_Author_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'about_author',
			__('weisay关于博主', 'weisaygrace_theme'),
			array('description' => __('显示博主信息和统计', 'weisaygrace_theme'))
		);
	}

	private function get_defaults() {
		// 获取主题目录下的默认背景图
		$default_cover = get_template_directory_uri() . '/assets/images/cover.jpg';

		return array(
			'name' => get_bloginfo('name'),
			'email' => get_bloginfo('admin_email'),
			'description' => get_bloginfo('description'),
			'cover' => $default_cover, // 默认背景图
			'show_stats' => array('posts', 'comments') // 默认显示的统计项
		);
	}

	// 前端显示
	public function widget($args, $instance) {
		$instance = wp_parse_args($instance, $this->get_defaults());
		echo $args['before_widget'];

		// 获取各种统计信息
		$stats = array();
		global $wpdb;

		// 文章数
		if (in_array('posts', $instance['show_stats'])) {
			$post_count = wp_count_posts();
			$published_posts = $post_count->publish;
			$stats['posts'] = array(
				'number' => number_format($published_posts),
				'label' => __('文章', 'weisaygrace_theme')
			);
		}

		// 评论数（判断>0）
		if (in_array('comments', $instance['show_stats'])) {
			$comment_count = wp_count_comments();
			$approved_comments = $comment_count->approved;
			if ($approved_comments > 0) {
				$stats['comments'] = array(
					'number' => number_format($approved_comments),
					'label' => __('评论', 'weisaygrace_theme')
				);
			}
		}

		// 分类数
		if (in_array('categories', $instance['show_stats'])) {
			$categories_count = wp_count_terms('category');
			$stats['categories'] = array(
				'number' => number_format($categories_count),
				'label' => __('分类', 'weisaygrace_theme')
			);
		}

		// 页面数（判断>0）
		if (in_array('pages', $instance['show_stats'])) {
			$pages_count = wp_count_posts('page');
			$published_pages = $pages_count->publish;
			if ($published_pages > 0) {
				$stats['pages'] = array(
					'number' => number_format($published_pages),
					'label' => __('页面', 'weisaygrace_theme')
				);
			}
		}

		// 友情链接数量（判断>0）
		if (in_array('links', $instance['show_stats'])) {
			$links_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'");
			if ($links_count > 0) {
				$stats['links'] = array(
					'number' => number_format($links_count),
					'label' => __('友链', 'weisaygrace_theme')
				);
			}
		}

		// 标签数量（判断>0）
		if (in_array('tags', $instance['show_stats'])) {
			$tags_count = wp_count_terms('post_tag');
			if ($tags_count > 0) {
				$stats['tags'] = array(
					'number' => number_format($tags_count),
					'label' => __('标签', 'weisaygrace_theme')
				);
			}
		}

		// 如果没有任何统计数据，至少显示文章和分类
		if (empty($stats) && (in_array('posts', $instance['show_stats']) || in_array('categories', $instance['show_stats']))) {
			$post_count = wp_count_posts();
			$stats['posts'] = array(
				'number' => number_format($post_count->publish),
				'label' => __('文章', 'weisaygrace_theme')
			);
			
			$categories_count = wp_count_terms('category');
			$stats['categories'] = array(
				'number' => number_format($categories_count),
				'label' => __('分类', 'weisaygrace_theme')
			);
		}
		?>
		<div class="about-author">
			<?php if (!empty($instance['cover'])) : ?>
				<div class="author-cover" style="background-image: url('<?php echo esc_url($instance['cover']); ?>')"></div>
			<?php endif; ?>
			
			<div class="author-avatar">
				<?php echo get_avatar($instance['email'], 70, '', $instance['name'], array('class' => 'avatar')); ?>
			</div>
			
			<div class="author-info">
				<h3 class="author-name"><?php echo esc_html($instance['name']); ?></h3>
				
				<?php if (!empty($instance['description'])) : ?>
					<div class="author-description">
						<?php echo wp_kses_post($instance['description']); ?>
					</div>
				<?php endif; ?>
				
				<?php if (!empty($stats)) : ?>
					<div class="author-stats">
						<?php foreach ($stats as $stat) : ?>
							<div class="stat-item">
								<span class="stat-number"><?php echo $stat['number']; ?></span>
								<span class="stat-label"><?php echo $stat['label']; ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		
		<?php
		echo $args['after_widget'];
	}

	// 后台表单
	public function form($instance) {
		$instance = wp_parse_args($instance, $this->get_defaults());
		if (!is_array($instance['show_stats'])) {
			$instance['show_stats'] = $this->get_defaults()['show_stats'];
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('name'); ?>"><strong><?php _e('博主名字', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="text" value="<?php echo esc_attr($instance['name']); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><strong><?php _e('自我介绍', 'weisaygrace_theme'); ?></strong></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" rows="3"><?php echo esc_textarea($instance['description']); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('email'); ?>"><strong><?php _e('邮箱（仅用于获取头像）', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="email" value="<?php echo esc_attr($instance['email']); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('cover'); ?>"><strong><?php _e('顶部背景图url', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cover'); ?>"  name="<?php echo $this->get_field_name('cover'); ?>" type="text" value="<?php echo esc_url($instance['cover']); ?>">
			<small><?php _e('可以替换图片地址更换背景图片', 'weisaygrace_theme'); ?></small>
		</p>
		<p>
			<label><strong><?php _e('显示统计', 'weisaygrace_theme'); ?></strong></label><br>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_stats_posts'); ?>" name="<?php echo $this->get_field_name('show_stats'); ?>[]" value="posts" <?php checked(in_array('posts', $instance['show_stats'])); ?>>
			<label for="<?php echo $this->get_field_id('show_stats_posts'); ?>"><?php _e('文章数', 'weisaygrace_theme'); ?></label><br>
			
			<input type="checkbox" id="<?php echo $this->get_field_id('show_stats_comments'); ?>" name="<?php echo $this->get_field_name('show_stats'); ?>[]" value="comments" <?php checked(in_array('comments', $instance['show_stats'])); ?>>
			<label for="<?php echo $this->get_field_id('show_stats_comments'); ?>"><?php _e('评论数', 'weisaygrace_theme'); ?></label><br>
			
			<input type="checkbox" id="<?php echo $this->get_field_id('show_stats_categories'); ?>" name="<?php echo $this->get_field_name('show_stats'); ?>[]" value="categories" <?php checked(in_array('categories', $instance['show_stats'])); ?>>
			<label for="<?php echo $this->get_field_id('show_stats_categories'); ?>"><?php _e('分类数', 'weisaygrace_theme'); ?></label><br>
			
			<input type="checkbox" id="<?php echo $this->get_field_id('show_stats_pages'); ?>" name="<?php echo $this->get_field_name('show_stats'); ?>[]" value="pages" <?php checked(in_array('pages', $instance['show_stats'])); ?>>
			<label for="<?php echo $this->get_field_id('show_stats_pages'); ?>"><?php _e('页面数', 'weisaygrace_theme'); ?></label><br>
			
			<input type="checkbox" id="<?php echo $this->get_field_id('show_stats_links'); ?>" name="<?php echo $this->get_field_name('show_stats'); ?>[]" value="links" <?php checked(in_array('links', $instance['show_stats'])); ?>>
			<label for="<?php echo $this->get_field_id('show_stats_links'); ?>"><?php _e('友链数', 'weisaygrace_theme'); ?></label><br>
			
			<input type="checkbox" id="<?php echo $this->get_field_id('show_stats_tags'); ?>" name="<?php echo $this->get_field_name('show_stats'); ?>[]" value="tags" <?php checked(in_array('tags', $instance['show_stats'])); ?>>
			<label for="<?php echo $this->get_field_id('show_stats_tags'); ?>"><?php _e('标签数', 'weisaygrace_theme'); ?></label>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['name'] = sanitize_text_field($new_instance['name']);
		$instance['description'] = wp_kses_post($new_instance['description']);
		$instance['email'] = sanitize_email($new_instance['email']);
		$instance['cover'] = esc_url_raw($new_instance['cover']);
		
		// 处理统计显示选项
		$instance['show_stats'] = array();
		if (!empty($new_instance['show_stats']) && is_array($new_instance['show_stats'])) {
			$allowed_stats = array('posts', 'comments', 'categories', 'pages', 'links', 'tags');
			foreach ($new_instance['show_stats'] as $stat) {
				if (in_array($stat, $allowed_stats)) {
					$instance['show_stats'][] = $stat;
				}
			}
		}
		
		return $instance;
	}
}

function register_about_author_widget() {
	register_widget('About_Author_Widget');
}
add_action('widgets_init', 'register_about_author_widget');

//读者墙小工具
class Reader_Wall_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'reader_wall',
			__('weisay读者墙', 'weisaygrace_theme'),
			array('description' => __('显示近期活跃评论者的头像墙', 'weisaygrace_theme'))
		);
	}

	// 小工具前端显示
	public function widget($args, $instance) {
		$title = apply_filters('widget_title', $instance['title']);
		$days = isset($instance['days']) ? absint($instance['days']) : 365;
		$limit = isset($instance['limit']) ? absint($instance['limit']) : 15;
		$show_count = !empty($instance['show_count']) ? true : false;

		echo $args['before_widget'];
		if (!empty($title)) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		global $wpdb;
		$my_email = get_bloginfo('admin_email');
		
		$query = $wpdb->prepare(
			"SELECT COUNT(comment_ID) AS cnt, comment_author, comment_author_url, comment_author_email 
			FROM (SELECT * FROM $wpdb->comments 
			LEFT OUTER JOIN $wpdb->posts ON ($wpdb->posts.ID=$wpdb->comments.comment_post_ID) 
			WHERE comment_date > date_sub(NOW(), INTERVAL %d DAY) 
			AND comment_author_email != %s 
			AND post_password = '' 
			AND comment_author_url != '' 
			AND comment_approved = '1' 
			AND (comment_type = '' OR comment_type = 'comment')) AS tempcmt 
			GROUP BY comment_author_email 
			ORDER BY cnt DESC 
			LIMIT %d",
			$days,
			$my_email,
			$limit
		);

		$wall = $wpdb->get_results($query);
		
		echo '<ul class="reader-wall-list">';
		foreach ($wall as $comment) {
			$url = $comment->comment_author_url ? esc_url($comment->comment_author_url) : '#';
			$title_text = esc_attr($comment->comment_author);
			
			if ($show_count) {
				$title_text .= esc_attr('（留下' . $comment->cnt . '个脚印）');
			}
			
			echo '<li>';
			echo '<a href="' . $url . '" rel="external nofollow" title="' . $title_text . '">';
			echo get_avatar($comment->comment_author_email, 60, '', $comment->comment_author);
			echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
		
		echo $args['after_widget'];
	}

	// 小工具后台表单
	public function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('读者墙', 'weisaygrace_theme');
		$days = isset($instance['days']) ? absint($instance['days']) : 365;
		$limit = isset($instance['limit']) ? absint($instance['limit']) : 15;
		$show_count = isset($instance['show_count']) ? (bool)$instance['show_count'] : true;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('标题', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('days'); ?>"><strong><?php _e('统计多少天内的评论', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name('days'); ?>" type="number" min="1" value="<?php echo $days; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><strong><?php _e('显示数量', 'weisaygrace_theme'); ?></strong></label>
			<input class="small-text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" min="1" value="<?php echo $limit; ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" <?php checked($show_count); ?> />
			<label for="<?php echo $this->get_field_id('show_count'); ?>"><strong><?php _e('title中显示用户的评论数量', 'weisaygrace_theme'); ?></strong></label>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
		$instance['days'] = (!empty($new_instance['days'])) ? absint($new_instance['days']) : 365;
		$instance['limit'] = (!empty($new_instance['limit'])) ? absint($new_instance['limit']) : 15;
		$instance['show_count'] = !empty($new_instance['show_count']);
		return $instance;
	}
}

function register_reader_wall_widget() {
	register_widget('Reader_Wall_Widget');
}
add_action('widgets_init', 'register_reader_wall_widget');

//文章目录小工具
class Article_Index_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'article_index',
			'weisay文章目录',
			array('description' => '显示当前文章/页面的多级目录索引')
		);

		add_filter('the_content', array($this, 'add_anchor_to_headings'), 10);
	}

	// 锚点添加方法
	public function add_anchor_to_headings($content) {
		global $post;
		
		// 只在文章和页面处理
		if (!is_single() && !is_page()) {
			return $content;
		}
		
		// 获取小工具实例设置
		$widget_options = get_option($this->option_name);
		$min_headers = 2; // 默认值
		
		if ($widget_options) {
			foreach ($widget_options as $number => $instance) {
				if (isset($instance['min_headers'])) {
					$min_headers = $instance['min_headers'];
					break;
				}
			}
		}
		
		// 处理标题锚点
		$matches = array();
		preg_match_all('/<h([2-6])(.*?)>(.*?)<\/h[2-6]>/is', $content, $matches);
		
		if (count($matches[0]) >= $min_headers) {
			foreach ($matches[1] as $key => $value) {
				$title = trim(strip_tags($matches[3][$key]));
				$content = str_replace(
					$matches[0][$key], 
					'<h' . $value . ' id="title-' . $key . '"' . $matches[2][$key] . '>' . $title . '</h' . $value . '>', 
					$content
				);
			}
		}
		
		return $content;
	}

	public function widget($args, $instance) {
		if (!is_single() && !is_page()) return;
		
		global $post;
		$min_headers = isset($instance['min_headers']) ? absint($instance['min_headers']) : 2;
		$widget_title = !empty($instance['title']) ? $instance['title'] : '文章目录';
		$depth_level = isset($instance['depth_level']) ? absint($instance['depth_level']) : 0; // 0表示不限制深度
		
		// 检查标题数量
		$matches = array();
		preg_match_all('/<h([2-6])(.*?)>(.*?)<\/h[2-6]>/is', $post->post_content, $matches);
		
		if (count($matches[0]) < $min_headers) return;
		
		// 构建完整目录
		$html = '';
		$index = 0;
		$this->buildDirectory($matches[1], $matches[3], $index, $html);
		
		// 根据深度设置过滤目录
		if ($depth_level > 0) {
			$html = $this->filterDirectoryByDepth($html, $depth_level);
		}
		
		// 输出
		echo $args['before_widget'];
		echo $args['before_title'] . $widget_title . $args['after_title'];
		echo '<div class="article-index-widget">' . $html . '</div>';
		echo $args['after_widget'];
	}
	
	// 构建多级目录
	private function buildDirectory($titleIndexArr, $titleContentArr, &$index, &$html) {
		$size = sizeof($titleIndexArr);
		$flag = $index == 0;
		$html .= $flag ? '<ol>' : '<ul>';
		
		while ($index < $size) {
			$title = trim(strip_tags($titleContentArr[$index]));
			$h = $titleIndexArr[$index];
			$html .= '<li><a href="#title-' . $index . '">' . esc_html($title) . "</a></li>";
			
			if ($index == $size - 1) {
				$index++;
				break;
			}
			
			$next_h = $titleIndexArr[++$index];
			if ($h < $next_h) {
				$this->buildDirectory($titleIndexArr, $titleContentArr, $index, $html);
				if ($index >= $size || $h > $titleIndexArr[$index]) {
					break;
				}
			} else if ($h > $next_h) {
				break;
			}
		}
		
		$html .= $flag ? '</ol>' : '</ul>';
	}
	
	// 根据深度过滤目录
	private function filterDirectoryByDepth($html, $depth_level) {
		$dom = new DOMDocument();
		@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		
		$xpath = new DOMXPath($dom);
		
		$ul_elements = $xpath->query('//ul[count(ancestor::ul) >= ' . ($depth_level - 1) . ']');
		
		foreach ($ul_elements as $ul) {
			// 直接移除整个ul元素及其内容
			$ul->parentNode->removeChild($ul);
		}
		
		// 返回处理后的HTML
		$result = '';
		foreach ($dom->childNodes as $node) {
			$result .= $dom->saveHTML($node);
		}
		return $result;
	}
	
	// 后台表单
	public function form($instance) {
		$title = !empty($instance['title']) ? $instance['title'] : '文章目录';
		$min_headers = isset($instance['min_headers']) ? absint($instance['min_headers']) : 2;
		$depth_level = isset($instance['depth_level']) ? absint($instance['depth_level']) : 0;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><strong><?php _e('标题', 'weisaygrace_theme'); ?></strong></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('min_headers'); ?>"><strong><?php _e('需要几个h标签才显示目录', 'weisaygrace_theme'); ?></strong></label>
			<select class="widefat" id="<?php echo $this->get_field_id('min_headers'); ?>" name="<?php echo $this->get_field_name('min_headers'); ?>">
				<option value="2" <?php selected($min_headers, 2); ?>><?php _e('2个及以上', 'weisaygrace_theme'); ?></option>
				<option value="3" <?php selected($min_headers, 3); ?>><?php _e('3个及以上', 'weisaygrace_theme'); ?></option>
				<option value="4" <?php selected($min_headers, 4); ?>><?php _e('4个及以上', 'weisaygrace_theme'); ?></option>
				<option value="5" <?php selected($min_headers, 5); ?>><?php _e('5个及以上', 'weisaygrace_theme'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('depth_level'); ?>"><strong><?php _e('目录显示深度', 'weisaygrace_theme'); ?></strong></label>
			<select class="widefat" id="<?php echo $this->get_field_id('depth_level'); ?>" name="<?php echo $this->get_field_name('depth_level'); ?>">
				<option value="0" <?php selected($depth_level, 0); ?>><?php _e('不限制深度', 'weisaygrace_theme'); ?></option>
				<option value="1" <?php selected($depth_level, 1); ?>><?php _e('1层深度', 'weisaygrace_theme'); ?></option>
				<option value="2" <?php selected($depth_level, 2); ?>><?php _e('2层深度', 'weisaygrace_theme'); ?></option>
				<option value="3" <?php selected($depth_level, 3); ?>><?php _e('3层深度', 'weisaygrace_theme'); ?></option>
				<option value="4" <?php selected($depth_level, 4); ?>><?php _e('4层深度', 'weisaygrace_theme'); ?></option>
			</select>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '文章目录';
		$instance['min_headers'] = isset($new_instance['min_headers']) ? absint($new_instance['min_headers']) : 2;
		$instance['depth_level'] = isset($new_instance['depth_level']) ? absint($new_instance['depth_level']) : 0;
		return $instance;
	}
}

function register_article_index_widget() {
	register_widget('Article_Index_Widget');
}
add_action('widgets_init', 'register_article_index_widget');

?>