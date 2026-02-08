<?php
function optionsframework_option_name() {
	return 'weisaygrace';
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

	$editor_setting = array(
		'quicktags' => 1,
		'tinymce' => 0,
		'media_buttons' => 0,
		'textarea_rows' => 4
	);

	$show_hide = array(
		'hide' => __( '隐藏', 'theme-textdomain' ),
		'display' => __( '显示', 'theme-textdomain' )
	);

	$on_off = array(
		'close' => __( '禁用', 'theme-textdomain' ),
		'open' => __( '启用', 'theme-textdomain' )
	);

	$options = array();

	$options[] = array(
		'name' => __( '全局设置', 'theme-textdomain' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( '主题使用说明', 'theme-textdomain' ),
		'desc' => sprintf( __( '详细使用说明请点击 <a href="%1$s" target="_blank">WordPress主题『Weisay Grace』</a>，若有疑问可以评论留言。', 'theme-textdomain' ), 'https://www.weisay.com/blog/wordpress-theme-weisay-grace.html?theme' ),
		'type' => 'info'
	);

	$options[] = array(
		'name' => __( '建站日期', 'theme-textdomain' ),
		'desc' => __( '必填，用于归档页统计建站天数及文章底部版权年份显示。格式示例：2007-04-22', 'theme-textdomain' ),
		'id' => 'wei_websitedate',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( 'SEO相关', 'theme-textdomain' ),
		'id' => 'wei_about_seo',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => __( '描述（Description）', 'theme-textdomain' ),
		'desc' => __( '输入你的网站描述，一般不超过200个字符', 'theme-textdomain' ),
		'id' => 'wei_description',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __( '关键词（KeyWords）', 'theme-textdomain' ),
		'desc' => __( '输入你的网站关键字，一般不超过100个字符', 'theme-textdomain' ),
		'id' => 'wei_keywords',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => __( '启用 Open Graph（OG）', 'theme-textdomain' ),
		'desc' => __( '默认禁用。开启后，页面 <code>&lt;head&gt;</code> 中会添加元数据，用于丰富社交分享信息，同时部分搜索引擎也支持这些数据', 'theme-textdomain' ),
		'id' => 'wei_opengraph',
		'std' => 'close',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'id' => 'distinguish',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => __( '网站页头自定义', 'theme-textdomain' ),
		'desc' => __( '在页面 <code>&lt;head&gt;</code> 中添加自定义代码。可用于插入内联 CSS、JavaScript 或其他代码，如统计脚本', 'theme-textdomain' ),
		'id' => 'wei_headcustom',
		'std' => '',
		'type' => 'editor',
		'settings' => $editor_setting
	);

	$options[] = array(
		'name' => __( '底部相关', 'theme-textdomain' ),
		'id' => 'wei_about_foot',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => '底部信息布局',
		'id' => 'wei_footlayout',
		'class' => 'sub-level',
		'std' => 'layout_lr',
		'type' => 'radio',
		'options' => array(
			'layout_lr' => '左右布局',
			'layout_c' => '居中布局',
		)
	);

	$options[] = array(
		'name' => __( '网站底部(左侧/第一行)自定义', 'theme-textdomain' ),
		'desc' => __( '输入你的自定义内容，支持html', 'theme-textdomain' ),
		'id' => 'wei_custom1',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'editor',
		'settings' => $editor_setting
	);

	$options[] = array(
		'name' => __( '网站底部(右侧/第二行)自定义', 'theme-textdomain' ),
		'desc' => __( '输入你的自定义内容，支持html', 'theme-textdomain' ),
		'id' => 'wei_custom2',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'editor',
		'settings' => $editor_setting
	);

	$options[] = array(
		'name' => __( '备案相关', 'theme-textdomain' ),
		'id' => 'wei_about_beian',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => __( '显示 ICP 备案号', 'theme-textdomain' ),
		'desc' => __( '默认隐藏', 'theme-textdomain' ),
		'id' => 'wei_beian',
		'class' => 'sub-level',
		'std' => 'hide',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( 'ICP 备案号', 'theme-textdomain' ),
		'desc' => __( '填写备案号，如：沪ICP备20250422号', 'theme-textdomain' ),
		'id' => 'wei_beianhao',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '显示公网安备案号', 'theme-textdomain' ),
		'desc' => __( '默认隐藏', 'theme-textdomain' ),
		'id' => 'wei_gwab',
		'class' => 'sub-level',
		'std' => 'hide',
		'type' => 'select',
		'options' => $show_hide
	);	

	$options[] = array(
		'name' => __( '公网安备案号', 'theme-textdomain' ),
		'desc' => __( '填写公网安备案号，如：京公网安备 11010102002019号', 'theme-textdomain' ),
		'id' => 'wei_gwabh',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '基础功能设置', 'theme-textdomain' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( '自定义颜色', 'theme-textdomain' ),
		'desc' => __( '选择自己喜欢的颜色作为链接等相关颜色，不使用自定义颜色清除即可', 'theme-textdomain' ),
		'id' => 'wei_link_color',
		'std' => '',
		'type' => 'color'
	);

	$options[] = array(
		'name' => __( '首页布局', 'theme-textdomain' ),
		'id' => 'wei_layout',
		'std' => 'blog',
		'type' => 'radio',
		'options' => array(
			'blog' => '博客布局',
			'card' => '卡片布局',
		)
	);

	$options[] = array(
		'name' => __( '卡片布局设置', 'theme-textdomain' ),
		'id' => 'wei_about_card',
		'class' => 'sub-level',
		'type' => 'info',
	);

	$options[] = array(
		'name' => '',
		'desc' => '显示文章摘要',
		'id' => 'wei_layout_card_excerpt',
		'std' => '0',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => '',
		'desc' => '显示侧边栏',
		'id' => 'wei_layout_card_sidebar',
		'std' => '0',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => '',
		'desc' => '',
		'id' => 'wei_layout_card_col',
		'class' => 'sub-level hidden',
		'std' => '3',
		'type' => 'radio',
		'options' => array(
			'2' => '2列',
			'3' => '3列',
			'4' => '4列',
		)
	);

	$options[] = array(
		'name' => __( '启用旧版小工具', 'theme-textdomain' ),
		'desc' => __( '默认禁用。相比新的区块小工具，旧版小工具操作更简单', 'theme-textdomain' ),
		'id' => 'wei_widgets',
		'std' => 'close',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'name' => __( '启用经典编辑器', 'theme-textdomain' ),
		'desc' => __( '默认禁用。开启后请勿同时启用经典编辑器（Classic Editor）插件，以免产生冲突', 'theme-textdomain' ),
		'id' => 'wei_editor',
		'std' => 'close',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'name' => __( '加载区块编辑器内联样式', 'theme-textdomain' ),
		'desc' => __( '默认启用，前台页面 <code>&lt;head&gt;</code> 中会加载区块编辑器相关的内联样式。如果你只使用经典编辑器，建议禁用此选项。', 'theme-textdomain' ),
		'id' => 'wei_gutenberg_css',
		'std' => 'open',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'name' => __( '显示导航栏搜索框', 'theme-textdomain' ),
		'desc' => __( '默认显示', 'theme-textdomain' ),
		'id' => 'wei_search',
		'std' => 'display',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '显示留言页评论排行', 'theme-textdomain' ),
		'desc' => __( '默认显示', 'theme-textdomain' ),
		'id' => 'wei_hotreviewer',
		'std' => 'display',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '显示归档页博客统计信息', 'theme-textdomain' ),
		'desc' => __( '默认显示', 'theme-textdomain' ),
		'id' => 'wei_statistics',
		'std' => 'display',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '特色功能设置', 'theme-textdomain' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( '缩略图类型', 'theme-textdomain' ),
		'desc' => __( '选择缩略图显示的优先级，> 符号前面的优先显示', 'theme-textdomain' ),
		'id' => 'wei_thumbnail',
		'std' => 'one',
		'type' => 'select',
		'options' => array(
			'one' => __( '随机缩略图', 'theme-textdomain' ),
			'two' => __( '特色图片>自定义缩略图>随机缩略图', 'theme-textdomain' ),
			'three' => __( '特色图片>自定义缩略图>文章第一张图>随机缩略图', 'theme-textdomain' ),
		)
	);

	$options[] = array(
		'name' => __( 'Gravatar头像替换源', 'theme-textdomain' ),
		'desc' => __( '解决Gravatar可能无法显示的问题，默认使用Weavatar', 'theme-textdomain' ),
		'id' => 'wei_gravatar',
		'std' => 'one',
		'type' => 'radio',
		'options' => array(
			'zero' => __( '官方源', 'theme-textdomain' ),
			'one' => __( 'Weavatar源', 'theme-textdomain' ),
			'two' => __( 'Cravatar源', 'theme-textdomain' ),
			'three' => __( 'Loli.net源', 'theme-textdomain' ),
			'four' => __( 'sep.cc源', 'theme-textdomain' ),
			'five' => __( '自定义源', 'theme-textdomain' ),
		)
	);

	$options[] = array(
		'name' => __( '自定义 Gravatar 替换源', 'theme-textdomain' ),
		'desc' => __( '选择自定义源后，请输入域名，例如 avatar.example.com，不要填写 http:// 或 https://', 'theme-textdomain' ),
		'id' => 'wei_gravatar_custom',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '文章相关', 'theme-textdomain' ),
		'id' => 'wei_about_article',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => __( '显示文章底部标签(tag)数量', 'theme-textdomain' ),
		'desc' => __( '默认显示', 'theme-textdomain' ),
		'id' => 'wei_tagshow',
		'class' => 'sub-level',
		'std' => 'display',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '显示上一篇 / 下一篇缩略图', 'theme-textdomain' ),
		'desc' => __( '默认显示', 'theme-textdomain' ),
		'id' => 'wei_navimg',
		'class' => 'sub-level',
		'std' => 'display',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '相关日志类型', 'theme-textdomain' ),
		'desc' => __( '选择是否带缩略图的相关日志，默认带缩略图', 'theme-textdomain' ),
		'id' => 'wei_related',
		'class' => 'sub-level',
		'std' => 'one',
		'type' => 'select',
		'options' => array(
		'one' => __( '带缩略图', 'theme-textdomain' ),
		'two' => __( '不带缩略图', 'theme-textdomain' ),
		)
	);

	$options[] = array(
		'name' => __( '启用代码高亮(Prism.js)', 'theme-textdomain' ),
		'desc' => sprintf( __( '默认禁用，若启用可点击查看 <a href="%1$s" target="_blank">Prism.js 代码高亮使用方法</a>', 'theme-textdomain' ), 'https://www.weisay.com/blog/wordpress-theme-weisay-box.html#title-11' ),
		'id' => 'wei_prismjs',
		'class' => 'sub-level',
		'std' => 'close',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'name' => __( '文章二维码', 'theme-textdomain' ),
		'id' => 'wei_about_qrcode',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => __( '显示文章二维码', 'theme-textdomain' ),
		'desc' => __( '默认隐藏，启用后会在文章页标题下方展示当前文章链接的二维码', 'theme-textdomain' ),
		'id' => 'wei_qrcode',
		'class' => 'sub-level',
		'std' => 'hide',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '二维码中间logo', 'theme-textdomain' ),
		'desc' => __( '自定义二维码中间logo。可不填写，如填写，请使用完整图片 URL，需包含 http:// 或 https://', 'theme-textdomain' ),
		'id' => 'wei_qrcodeimg',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '评论相关', 'theme-textdomain' ),
		'id' => 'wei_about_comment',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => __( '显示评论者 IP 归属地', 'theme-textdomain' ),
		'desc' => __( '前台访客可显示简版 IP 归属地（省一级），管理员登录后可显示完整归属地，IP 数据库来源：Ip2region', 'theme-textdomain' ),
		'id' => 'wei_ipshow',
		'class' => 'sub-level',
		'std' => 'hide',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( 'IP 归属地支持 IPv6', 'theme-textdomain' ),
		'desc' => sprintf( __( '默认仅支持IPv4，开启 IPv6 支持后，需要自行下载 <a href="%1$s" target="_blank">Ip2region的IPv6数据库</a>，并上传至主题目录 includes/ipdata/', 'theme-textdomain' ), 'https://ip2region.net/' ),
		'id' => 'wei_ipv6',
		'class' => 'sub-level',
		'std' => '2',
		'type' => 'select',
		'options' => array(
			'1' => '支持',
			'2' => '不支持',
		)
	);

	$options[] = array(
		'name' => __( '评论框位置', 'theme-textdomain' ),
		'desc' => __( '默认在评论列表下方', 'theme-textdomain' ),
		'id' => 'wei_abovecomments',
		'class' => 'sub-level',
		'std' => '1',
		'type' => 'select',
		'options' => array(
			'1' => '评论列表下方',
			'2' => '评论列表上方',
		)
	);

	$options[] = array(
		'name' => __( '屏蔽非中文评论', 'theme-textdomain' ),
		'desc' => __( '默认禁用，开启后，评论内容必须包含中文，否则无法提交', 'theme-textdomain' ),
		'id' => 'wei_chinese',
		'class' => 'sub-level',
		'std' => 'close',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'name' => __( '启用评论打字特效', 'theme-textdomain' ),
		'desc' => __( '默认禁用，开启后，输入评论内容时有礼花特效', 'theme-textdomain' ),
		'id' => 'wei_typing',
		'class' => 'sub-level',
		'std' => 'close',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'name' => __( '走心评论相关', 'theme-textdomain' ),
		'id' => 'wei_about_touching',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => __( '启用走心评论', 'theme-textdomain' ),
		'desc' => __( '默认禁用', 'theme-textdomain' ),
		'id' => 'wei_touching',
		'class' => 'sub-level',
		'std' => 'close',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'name' => __( '显示走心评论页顶部随机图', 'theme-textdomain' ),
		'desc' => __( '默认显示', 'theme-textdomain' ),
		'id' => 'wei_tcbgimg',
		'class' => 'sub-level',
		'std' => 'display',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '走心评论页子标题', 'theme-textdomain' ),
		'desc' => __( '自定义子标题，仅在显示随机背景图片时生效，未填写时显示默认文案：「每一条评论，都是一个故事！」', 'theme-textdomain' ),
		'id' => 'wei_tctagline',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '走心评论页链接', 'theme-textdomain' ),
		'desc' => __( '评论中入选走心评论的链接。可不填写，如填写，请使用完整 URL，需包含 http:// 或 https://', 'theme-textdomain' ),
		'id' => 'wei_touchingurl',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '走心评论页列数', 'theme-textdomain' ),
		'desc' => __( '此设置只针对pc端，移动端根据宽度自适应', 'theme-textdomain' ),
		'id' => 'wei_touchingcol',
		'class' => 'sub-level',
		'std' => '4',
		'type' => 'radio',
		'options' => array(
			'1' => '1列',
			'2' => '2列',
			'3' => '3列',
			'4' => '4列',
		)
	);

	$options[] = array(
		'name' => __( '评论邮件设置', 'theme-textdomain' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => '',
		'desc' => __( '提示：如果使用本主题自带的评论回复邮件通知功能，请勿同时启用类似插件，以免产生冲突。', 'theme-textdomain' ),
		'class' => 'tips',
		'type' => 'info'
	);

	$options[] = array(
		'name' => __( '启用评论邮件通知', 'theme-textdomain' ),
		'desc' => __( '默认禁用，开启后，如 SMTP 功能正常，管理员回复评论时将邮件通知原评论用户', 'theme-textdomain' ),
		'id' => 'wei_smtp',
		'std' => 'close',
		'type' => 'select',
		'options' => $on_off
	);

	$options[] = array(
		'name' => __( '发送测试邮件', 'theme-textdomain' ),
		'id' => 'wei_test_mail_info',
		'type' => 'info',
		'desc' => '完成下方的 SMTP 配置后，点击右侧按钮发送测试邮件到管理员邮箱（<strong>确保已开启上方的评论邮件通知</strong>）<br><br>
			<button type="button" id="wei_send_test_mail" class="button button-primary">发送测试邮件</button>
			<span id="wei_test_mail_result" style="margin-left:10px;"></span>'
	);

	$options[] = array(
		'name' => __( '普通用户回复他人时通知对方', 'theme-textdomain' ),
		'desc' => __( '默认不通知，开启后请谨慎使用，以避免垃圾评论打扰用户', 'theme-textdomain' ),
		'id' => 'wei_notify_user',
		'std' => '2',
		'type' => 'radio',
		'options' => array(
			'1' => '通知',
			'2' => '不通知',
		)
	);

	$options[] = array(
		'name' => __( 'SMTP 邮件设置', 'theme-textdomain' ),
		'id' => 'wei_about_smtp',
		'class' => 'separate',
		'type' => 'info',
	);

	$options[] = array(
		'name' => __( 'SMTP 服务器地址', 'theme-textdomain' ),
		'desc' => __( '常见的SMTP服务器地址包括smtp.qq.com、smtp.163.com、smtp.126.com、smtp.gmail.com等', 'theme-textdomain' ),
		'id' => 'wei_smtp_host',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( 'SMTP 端口', 'theme-textdomain' ), 'SMTP 端口',
		'desc' => __( 'SMTP服务器的端口号通常为465或者587，具体取决于您的邮件服务提供商要求的设置', 'theme-textdomain' ),
		'id' => 'wei_smtp_port',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( 'SMTP 加密方式', 'theme-textdomain' ),
		'desc' => __( '通常可以选择SSL/TLS加密方式来确保邮件传输的安全性', 'theme-textdomain' ),
		'id' => 'wei_smtp_secure',
		'class' => 'sub-level',
		'std' => 'ssl',
		'type' => 'select',
		'options' => array(
			'ssl' => 'SSL',
			'tls' => 'TLS',
			'' => '无加密'
		)
	);

	$options[] = array(
		'name' => __( 'SMTP 登录邮箱', 'theme-textdomain' ),
		'desc' => __( '请输入SMTP邮箱地址', 'theme-textdomain' ),
		'id' => 'wei_smtp_username',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( 'SMTP 授权码/密码', 'theme-textdomain' ),
		'desc' => __( '建议使用授权码', 'theme-textdomain' ),
		'id' => 'wei_smtp_password',
		'class' => 'sub-level',
		'std' => '',
		'type' => 'password'
	);

	$options[] = array(
		'name' => __( '发件人名称', 'theme-textdomain' ),
		'id' => 'wei_smtp_from_name',
		'class' => 'sub-level',
		'std' => get_bloginfo('name'),
		'type' => 'text'
	);

	$options[] = array(
		'name' => __( '打赏设置', 'theme-textdomain' ),
		'type' => 'heading'
	);

	$options[] = array(
		'name' => __( '显示文章底部作者信息', 'theme-textdomain' ),
		'desc' => __( '默认显示', 'theme-textdomain' ),
		'id' => 'wei_author_info',
		'std' => 'display',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '显示文章底部打赏', 'theme-textdomain' ),
		'desc' => __( '默认隐藏，开启此功能前，请确保文章底部作者信息已显示', 'theme-textdomain' ),
		'id' => 'wei_reward',
		'std' => 'hide',
		'type' => 'select',
		'options' => $show_hide
	);

	$options[] = array(
		'name' => __( '支付宝收款二维码图片', 'theme-textdomain' ),
		'desc' => __( '支付宝收款二维码图片，大小建议：170px*170px', 'theme-textdomain' ),
		'id' => 'wei_alipay',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __( '微信收款二维码图片', 'theme-textdomain' ),
		'desc' => __( '微信收款二维码图片，大小建议：170px*170px', 'theme-textdomain' ),
		'id' => 'wei_wxpay',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => __( '更新日志', 'theme-textdomain' ),
		'type' => 'heading'
	);

	$options[] = array(
		'desc' => get_changelog_content(),
		'id' => 'wei_changelog',
		'type' => 'info'
	);

	return $options;
}