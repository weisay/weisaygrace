<?php
function optionsframework_option_name() {
	return 'weisaygrace-theme';
}

// 读取changelog.txt 更新日志文件
function get_changelog_content() {
	$changelog_file = get_template_directory() . '/changelog.txt';
	if (!file_exists($changelog_file)) return '<div class="update-item"><p>暂无更新日志</p></div>';
	$lines = file($changelog_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$changelog_html = '';
	$current_version = '';
	foreach ($lines as $line) {
		$line = trim($line);
		if (empty($line)) continue;
		if (strpos($line, "\t\t") !== false) {
			if (!empty($current_version)) $changelog_html .= '</ol></div>';
			list($version, $date) = explode("\t\t", $line, 2);
			$changelog_html .= '<div class="update-item"><h4 class="heading">版本 ' 
				. esc_html(trim($version)) . '<span class="update-date">' 
				. esc_html(trim($date)) . '</span></h4><ol class="changelog">';
			$current_version = $version;
		} else {
			$changelog_html .= '<li>' . esc_html($line) . '</li>';
		}
	}
	return !empty($current_version) ? $changelog_html . '</ol></div>' : '<div class="update-item"><p>暂无更新日志</p></div>';
}

function optionsframework_options() {
	$shortname = "wei";
	
	$whether_array = array(
		'hide' => __( '隐藏', 'theme-textdomain' ),
		'display' => __( '展示', 'theme-textdomain' )
	);
	
	$whether_arrays = array(
		'close' => __( '关闭', 'theme-textdomain' ),
		'open' => __( '开启', 'theme-textdomain' )
	);
	
	$thumbnail_array = array(
		'one' => __( '随机缩略图', 'theme-textdomain' ),
		'two' => __( '特色图片>自定义缩略图>随机缩略图', 'theme-textdomain' ),
		'three' => __( '特色图片>自定义缩略图>文章第一张图>随机缩略图', 'theme-textdomain' ),
	);
	
	$gravatar_array = array(
		'one' => __( 'Weavatar源', 'theme-textdomain' ),
		'two' => __( 'Cravatar源', 'theme-textdomain' ),
		'three' => __( 'Loli源', 'theme-textdomain' ),
		'four' => __( 'sep.cc源', 'theme-textdomain' ),
	);
	
	$related_array = array(
		'one' => __( '带缩略图', 'theme-textdomain' ),
		'two' => __( '不带缩略图', 'theme-textdomain' ),
	);

	$options = array();

	$options[] = array(
		'name' => __( '全局设置', 'theme-textdomain' ),
		'type' => 'heading'
	);
	
	$options[] = array(
		'name' => __( '主题使用教程', 'theme-textdomain' ),
		'desc' => sprintf( __( '详细使用教程点击 <a href="%1$s" target="_blank">WordPress主题『Weisay Grace』</a>，若有疑问可以评论留言。', 'theme-textdomain' ), 'https://www.weisay.com/blog/wordpress-theme-weisay-grace.html?weisaygrace' ),
		'type' => 'info'
	);

	$options[] = array(
		'name' => __( '描述（Description）', 'theme-textdomain' ),
		'desc' => __( '输入你的网站描述，一般不超过200个字符', 'theme-textdomain' ),
		'id' => $shortname."_description",
		'std' => '',
		'type' => 'textarea'
	);
	
	$options[] = array(
		'name' => __( '关键词（KeyWords）', 'theme-textdomain' ),
		'desc' => __( '输入你的网站关键字，一般不超过100个字符', 'theme-textdomain' ),
		'id' => $shortname."_keywords",
		'std' => '',
		'type' => 'textarea'
	);
	
	$options[] = array(
		'name' => __( '是否展示ICP备案号', 'theme-textdomain' ),
		'desc' => __( '默认隐藏', 'theme-textdomain' ),
		'id' => $shortname."_beian",
		'std' => 'hide',
		'type' => 'select',
		'options' => $whether_array
	);

	$options[] = array(
		'name' => __( '输入您的ICP备案号', 'theme-textdomain' ),
		'desc' => __( '填写备案号，如：沪ICP备20250422号', 'theme-textdomain' ),
		'id' => $shortname."_beianhao",
		'std' => '',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => __( '是否展示公网安备案号', 'theme-textdomain' ),
		'desc' => __( '默认隐藏', 'theme-textdomain' ),
		'id' => $shortname."_gwab",
		'std' => 'hide',
		'type' => 'select',
		'options' => $whether_array
	);	
	
	$options[] = array(
		'name' => __( '输入您的公网安备案号', 'theme-textdomain' ),
		'desc' => __( '填写公网安备案号，如：京公网安备 11010102002019号', 'theme-textdomain' ),
		'id' => $shortname."_gwabh",
		'std' => '',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => __( '输入您的建站日期', 'theme-textdomain' ),
		'desc' => __( '必填，归档页面统计建站天数及底部版权年份展示使用。格式如：2007-04-22', 'theme-textdomain' ),
		'id' => $shortname."_websitedate",
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '基础功能设置', 'theme-textdomain' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( '是否展示导航栏的搜索框', 'theme-textdomain' ),
		'desc' => __( '默认展示', 'theme-textdomain' ),
		'id' => $shortname."_search",
		'std' => 'display',
		'type' => 'select',
		'options' => $whether_array
	);
	
	$options[] = array(
		'name' => __( '是否展示留言页面评论排行', 'theme-textdomain' ),
		'desc' => __( '默认展示', 'theme-textdomain' ),
		'id' => $shortname."_hotreviewer",
		'std' => 'display',
		'type' => 'select',
		'options' => $whether_array
	);

	$options[] = array(
		'name' => __( '是否展示归档页面博客统计信息', 'theme-textdomain' ),
		'desc' => __( '默认展示', 'theme-textdomain' ),
		'id' => $shortname."_statistics",
		'std' => 'display',
		'type' => 'select',
		'options' => $whether_array
	);
	
	$options[] = array(
		'name' => __( '是否启用旧版小工具', 'theme-textdomain' ),
		'desc' => __( '默认关闭。旧版小工具相比块编辑小工具要简单一些', 'theme-textdomain' ),
		'id' => $shortname."_widgets",
		'std' => 'close',
		'type' => 'select',
		'options' => $whether_arrays
	);
	
	$options[] = array(
		'name' => __( '特色功能设置', 'theme-textdomain' ),
		'type' => 'heading'
	);
	
	$options[] = array(
		'name' => __( '缩略图类型', 'theme-textdomain' ),
		'desc' => __( '选择缩略图展示的优先级，> 符号前面的优先展示', 'theme-textdomain' ),
		'id' => $shortname."_thumbnail",
		'std' => 'one',
		'type' => 'select',
		'options' => $thumbnail_array
	);
	
	$options[] = array(
		'name' => __( '是否开启Open Graph', 'theme-textdomain' ),
		'desc' => __( '默认关闭。Open Graph会在页面head中添加一些元数据来丰富社交分享信息，部分搜索引擎也支持', 'theme-textdomain' ),
		'id' => $shortname."_opengraph",
		'std' => 'close',
		'type' => 'select',
		'options' => $whether_arrays
	);
	
	$options[] = array(
		'name' => __( 'Gravatar头像替换源', 'theme-textdomain' ),
		'desc' => __( '解决Gravatar无法展示的问题，默认使用Weavatar', 'theme-textdomain' ),
		'id' => $shortname."_gravatar",
		'std' => 'one',
		'type' => 'select',
		'options' => $gravatar_array
	);
	
	$options[] = array(
		'name' => __( '是否前台显示评论者归属地', 'theme-textdomain' ),
		'desc' => __( '默认隐藏，访客前台可显示简版归属地，只显示到省一级；管理员登录后其前台展示完整归属地', 'theme-textdomain' ),
		'id' => $shortname."_ipshow",
		'std' => 'hide',
		'type' => 'select',
		'options' => $whether_array
	);
	
	$options[] = array(
		'name' => __( '是否显示文章底部标签(tag)的数量', 'theme-textdomain' ),
		'desc' => __( '默认显示', 'theme-textdomain' ),
		'id' => $shortname."_tagshow",
		'std' => 'display',
		'type' => 'select',
		'options' => $whether_array
	);
	
	$options[] = array(
		'name' => __( '相关日志类型', 'theme-textdomain' ),
		'desc' => __( '选择是否带缩略图的相关日志，默认带缩略图', 'theme-textdomain' ),
		'id' => $shortname."_related",
		'std' => 'one',
		'type' => 'select',
		'options' => $related_array
	);
	
	$options[] = array(
		'name' => __( '是否开启代码高亮功能(Prism.js)', 'theme-textdomain' ),
		'desc' => __( '默认关闭', 'theme-textdomain' ),
		'id' => $shortname."_prismjs",
		'std' => 'close',
		'type' => 'select',
		'options' => $whether_arrays
	);
	
	$options[] = array(
		'name' => __( '是否开启走心评论功能', 'theme-textdomain' ),
		'desc' => __( '默认关闭', 'theme-textdomain' ),
		'id' => $shortname."_touching",
		'std' => 'close',
		'type' => 'select',
		'options' => $whether_arrays
	);

	$options[] = array(
		'name' => __( '输入您的走心评论独立页面链接', 'theme-textdomain' ),
		'desc' => __( '填写完整链接地址，请包含http或者https', 'theme-textdomain' ),
		'id' => $shortname."_touchingurl",
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '打赏设置', 'theme-textdomain' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( '是否展示文章页打赏', 'theme-textdomain' ),
		'desc' => __( '默认隐藏', 'theme-textdomain' ),
		'id' => $shortname."_reward",
		'std' => 'hide',
		'type' => 'select',
		'options' => $whether_array
	);

	$options[] = array(
		'name' => __( '支付宝收款二维码图片', 'theme-textdomain' ),
		'desc' => __( '支付宝收款二维码图片，大小建议：170px*170px', 'theme-textdomain' ),
		'id' => $shortname."_alipay",
		'type' => 'upload'
	);
	
	$options[] = array(
		'name' => __( '微信收款二维码图片', 'theme-textdomain' ),
		'desc' => __( '微信收款二维码图片，大小建议：170px*170px', 'theme-textdomain' ),
		'id' => $shortname."_wxpay",
		'type' => 'upload'
	);
	
	$options[] = array(
		'name' => __( '更新日志', 'theme-textdomain' ),
		'type' => 'heading'
	);


	$options[] = array(
		'desc' => get_changelog_content(),
		'id' => $shortname . "_changelog",
		'type' => 'info'
	);

	return $options;
}