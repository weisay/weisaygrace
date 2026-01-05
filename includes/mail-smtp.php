<?php

// ==================== 统一 SMTP 发件人 ====================
add_filter('wp_mail_from', function($from){
	return weisay_option('wei_smtp_username');
}, 9999);

// ==================== SMTP 配置 ====================
add_action('phpmailer_init', 'custom_phpmailer_smtp');
function custom_phpmailer_smtp($phpmailer) {

	// 从后台获取配置
	$host = weisay_option('wei_smtp_host');
	$port = intval(weisay_option('wei_smtp_port'));
	$secure = weisay_option('wei_smtp_secure');
	$username = weisay_option('wei_smtp_username');
	$password = weisay_option('wei_smtp_password');
	$fromname = weisay_option('wei_smtp_from_name') ?: get_bloginfo('name');

	if (!$host || !$port || !$username || !$password) {
		return; // 未配置跳过
	}

	// 设置 SMTP
	$phpmailer->isSMTP();
	$phpmailer->Host = $host;
	$phpmailer->SMTPAuth = true;
	$phpmailer->Port = $port;
	$phpmailer->SMTPSecure = $secure;
	$phpmailer->Username = $username;
	$phpmailer->FromName = $fromname;
	$phpmailer->Password = $password;
	$phpmailer->setFrom($username, $fromname, false);
	$phpmailer->Sender = $username;
}

// ==================== 评论回复模板 ====================
function generate_comment_email_message($comment, $parent_comment) {
	if ( empty($parent_comment) && ! empty($comment->comment_parent) ) {
		$parent_comment = get_comment( intval($comment->comment_parent) );
	}
	$home_url = esc_url( get_option('home') );
	$blog_name = esc_html( get_option('blogname') );
	$post_link = esc_url( get_permalink($comment->comment_post_ID) );
	$post_title = esc_html( get_the_title($comment->comment_post_ID) );
	$parent_author = $parent_comment ? esc_html( trim($parent_comment->comment_author) ) : '';
	$parent_author_email = $parent_comment ? sanitize_email( $parent_comment->comment_author_email ) : '';
	$parent_content = $parent_comment ? wpautop(esc_html( $parent_comment->comment_content )) : '';
	$comment_author = esc_html( trim($comment->comment_author) );
	$comment_author_email = sanitize_email( $comment->comment_author_email );
	$comment_content = wpautop( esc_html( $comment->comment_content ) );
	$parent_comment_link = $parent_comment ? esc_url( get_comment_link($parent_comment->comment_ID) ) : esc_url( get_comment_link($comment->comment_ID) );

	$parent_avatar = $parent_author_email ? esc_url(get_avatar_url($parent_author_email, ['size'=>96])) : '';
	$comment_avatar = $comment_author_email ? esc_url(get_avatar_url($comment_author_email, ['size'=>96])) : '';

	ob_start();
	?>
	<table style="font-family:Arial,sans-serif;color:#333;margin:0;padding:0;max-width:820px;margin:0 auto;border-radius:0;" border="0" cellpadding="0" cellspacing="0">
		<tbody><tr>
			<td>
				<table style="margin-bottom:20px;" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody><tr>
						<td style="padding:10px 0;font-size:20px;text-align:left;vertical-align:middle;"><?php echo $parent_author; ?>, 您好!</td>
					</tr>
				</tbody></table>

				<table style="margin-bottom:20px;" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody><tr>
						<td style="font-size:16px;">
							您在 [ <strong><a style="text-decoration:none;color:#333;" href="<?php echo $home_url; ?>" target="_blank"><?php echo $blog_name; ?></a></strong> ]
							文章《<strong><a style="text-decoration:none;color:#da4453;" href="<?php echo $post_link; ?>" target="_blank"><?php echo $post_title; ?></a></strong>》 中的评论有了新回复：
						</td>
					</tr>
				</tbody></table>
				<table style="margin-bottom:20px;" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody><tr>
						<td width="100%">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tbody><tr>
									<td style="padding:10px;border-radius:8px;overflow:hidden;color:#333;background-color:#eef1f4;" >
										<div style="display:flex;align-items:center;justify-content:flex-start;">
											<div style="flex-shrink:0;margin-right:10px;">
												<img style="width:48px;height:48px;border-radius:50%;"
													alt="<?php echo esc_attr($parent_author); ?>"
													src="<?php echo $parent_avatar; ?>">
											</div>
											<div>
												<strong style="font-size:16px;"><?php echo $parent_author; ?></strong>
											</div>
										</div>
										<p style="margin-top:10px;margin-right:60px;line-height:26px;"><?php echo $parent_content; ?></p>
									</td>
								</tr>
							</tbody></table>
						</td>
					</tr>
				</tbody></table>
				<table style="margin-bottom:20px;" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody><tr>
						<td width="100%">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tbody><tr>
									<td style="padding:10px;text-align:right;border-radius:8px;overflow:hidden;color:#333;background-color:#fff1f3;" >
										<div style="display:flex;align-items:center;justify-content:flex-end;">
											<div>
												<strong style="font-size:16px;"><?php echo $comment_author; ?></strong>
											</div>
											<div style="flex-shrink:0;margin-left:10px;">
												<img style="width:48px;height:48px;border-radius:50%;"
													 alt="<?php echo esc_attr($comment_author); ?>"
													 src="<?php echo $comment_avatar; ?>">
											</div>
										</div>
										<p style="margin-top:10px;margin-left:60px;line-height:26px;"><?php echo $comment_content; ?></p>
									</td>
								</tr>
							</tbody></table>
						</td>
					</tr>
				</tbody></table>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody><tr>
						<td style="padding:20px 0 30px 0;" align="center">
							<a style="display:inline-block;background-color:#ed5565;color:white;text-decoration:none;padding:10px 20px;border-radius:4px;font-weight:bold;"
							href="<?php echo $parent_comment_link; ?>" target="_blank">查看完整内容</a>
						</td>
					</tr>
				</tbody></table>
				<table style="background-color:#f8f8f8;" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody><tr>
						<td style="color:#666;text-align:center;font-size:12px;padding:15px 0;" width="100%">
							(此邮件由系统自动发送，请勿回复！)
							<span style="display:block;padding-top:8px;border-bottom:1px solid #ccc"></span>
							<a style="display:inline-block;padding-top:8px;text-decoration:none;color:#333;font-size:14px;" href="<?php echo $home_url; ?>" target="_blank">© <?php echo $blog_name; ?></a>
						</td>
					</tr>
				</tbody></table>
			</td>
		</tr></tbody>
	</table>
	<?php
	return ob_get_clean();
}

// ==================== 通知管理员邮件模板 ====================
function generate_admin_comment_email($comment, $is_approved = true) {
	$blog_name = esc_html(get_option('blogname'));
	$post_title = esc_html(get_the_title($comment->comment_post_ID));
	$comment_url = esc_url(get_comment_link($comment->comment_ID));
	$post_link = esc_url(get_permalink($comment->comment_post_ID));
	$parent_comment = !empty($comment->comment_parent) ? get_comment($comment->comment_parent) : null;
	$comment_author = esc_html($comment->comment_author);
	$comment_author_email = sanitize_email($comment->comment_author_email);
	$comment_author_url = esc_url($comment->comment_author_url);
	$comment_content = wpautop(esc_html($comment->comment_content));
	$comment_type_text = $comment->comment_parent ? '回复' : '评论';
	if (!$is_approved) {
		$comment_type_text .= '等待审核';
	}

	ob_start();
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:820px;margin:0 auto;background-color:#ffffff;">
		<tbody>
			<tr>
				<td style="padding:20px;">
					<table width="100%" style="padding:10px 0;margin-bottom:16px;">
						<tr>
							<td style="font-size:16px;color:#5c4c51;font-weight:bold;">您的文章《<a style="text-decoration:none;color:#da4453;" href="<?php echo $post_link; ?>" target="_blank"><?php echo $post_title; ?></a>》收到了一条新<?php echo $comment_type_text; ?></td>
						</tr>
					</table>
					<table width="100%" style="background-color:#f9f9f9;border-radius:5px;padding:12px;margin-bottom:16px;">
						<tr>
							<td style="padding:8px 0;">
								<strong style="color:#555;width:80px;display:inline-block;">评论者：</strong>
								<span style="color:#333;"><?php echo $comment_author; ?></span>
							</td>
						</tr>
						<?php if ($comment_author_email): ?>
						<tr>
							<td style="padding:8px 0;">
								<strong style="color:#555;width:80px;display:inline-block;">邮箱：</strong>
								<span style="color:#333;"><?php echo $comment_author_email; ?></span>
							</td>
						</tr>
						<?php endif; ?>
						<?php if ($comment_author_url): ?>
						<tr>
							<td style="padding:8px 0;">
								<strong style="color:#555;width:80px;display:inline-block;">URL：</strong>
								<span style="color:#333;">
									<a href="<?php echo $comment_author_url; ?>" target="_blank"><?php echo $comment_author_url; ?></a>
								</span>
							</td>
						</tr>
						<?php endif; ?>
					</table>
					<?php if ($parent_comment): ?>
					<table width="100%" style="margin-bottom:8px;">
						<tr>
							<td style="font-size:18px;color:#333;">
								原 <?php echo esc_html($parent_comment->comment_author); ?> 发的评论：
							</td>
						</tr>
					</table>
					<table width="100%" style="margin-bottom:16px;">
						<tr>
							<td style="font-size:16px;line-height:28px;background-color:#f5f5f5;border-left:4px solid #999;padding:15px;color:#444;">
								<?php echo wpautop(esc_html($parent_comment->comment_content)); ?>
							</td>
						</tr>
					</table>
					<?php endif; ?>
					<table width="100%" style="margin-bottom:10px;">
						<tr>
							<td style="font-size:18px;color:#333;"><?php echo $comment_author; ?> <?php echo ($comment->comment_parent ? '回复' : '评论'); ?>如下：</td>
						</tr>
					</table>
					<table width="100%" style="margin-bottom:16px;">
						<tr>
							<td style="font-size:16px;line-height:30px;background-color:#fffafb;border-left:4px solid #ed5565;padding:15px;color:#333;">
								<?php echo $comment_content; ?>
							</td>
						</tr>
					</table>
					<table width="100%">
						<tr>
							<td style="padding:16px 0 32px 0;text-align:center;">
								<a href="<?php echo $post_link . '#comments'; ?>"
								   style="display:inline-block;background-color:#ed5565;color:white;text-decoration:none;padding:10px 20px;border-radius:4px;font-weight:bold;">
									查看所有评论
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
	return ob_get_clean();
}

// ==================== 发送邮件函数 ====================
function send_comment_email($to, $subject, $message) {
	$from_email = weisay_option('wei_smtp_username');
	$from_name = weisay_option('wei_smtp_from_name') ?: get_bloginfo('name');
	$headers = array(
		"From: $from_name <$from_email>",
		"Reply-To: $from_email",
		"Content-Type: text/html; charset=" . get_option('blog_charset')
	);
	return wp_mail($to, $subject, $message, $headers);
}

// ==================== 评论邮件通知 ====================
add_action('comment_post', 'handle_comment_email', 10, 2);
function handle_comment_email($comment_id, $comment_approved) {
	static $processing = false; // 防无限循环
	if ($processing) return;
	$processing = true;

	$comment = get_comment($comment_id);
	$admin_email = get_bloginfo('admin_email');
	$parent_id = $comment->comment_parent;
	$is_admin_commenter = ($comment->user_id && user_can($comment->user_id, 'manage_options'));
	$is_approved = in_array($comment_approved, [1, '1', 'approve', 'approved'], true);

	// 获取父评论及其管理员状态
	$parent_comment = $parent_id ? get_comment($parent_id) : null;
	$parent_is_admin = $parent_comment ? user_can(intval($parent_comment->user_id ?? 0), 'manage_options') : false;

	// 管理员回复逻辑
	if ($is_admin_commenter) {
		// 管理员回复普通用户 → 只通知被回复用户
		if ($parent_comment && !$parent_is_admin) {
			$email_send_user = generate_comment_email_message($comment, $parent_comment);
			$email_send_user = convert_smilies($email_send_user);
			send_comment_email($parent_comment->comment_author_email, '✨您在 [' . esc_html(get_option('blogname')) . '] 的评论有了新回复', $email_send_user);
		}
		$processing = false;
		return;
	}

	// 普通用户默认已批准评论的回复逻辑
	$reply_to_self = $parent_comment && ($comment->comment_author_email === $parent_comment->comment_author_email);
	// 如果不是自己回复自己 → 判断后台开关是否通知被回复用户
	if ($parent_comment && !$reply_to_self && $is_approved && !$parent_is_admin && weisay_option('wei_notify_user') == '1') {
		$email_send_user = generate_comment_email_message($comment, $parent_comment);
		$email_send_user = convert_smilies($email_send_user);
		send_comment_email($parent_comment->comment_author_email, '✨您在 [' . esc_html(get_option('blogname')) . '] 的评论有了新回复', $email_send_user);
	}

	// 普通用户评论或回复（包括自己） → 判断后台开关是否通知管理员
	if (weisay_option('wei_notify_admin') == '1') {
		$blog_name = esc_html(get_option('blogname'));
		$post_title = esc_html(get_the_title($comment->comment_post_ID));
		$comment_type = $comment->comment_parent ? '回复' : '评论';
		$email_subject_prefix = $is_approved ? '' : '请审核：';
		$email_subject = $email_subject_prefix . "[{$blog_name}] 的「{$post_title}」有新{$comment_type}";
		$email_send_admin = generate_admin_comment_email($comment, $is_approved);
		$email_send_admin = convert_smilies($email_send_admin);
		send_comment_email($admin_email, $email_subject, $email_send_admin);
	}
	$processing = false;
}

// ==================== 待审核评论通过后邮件 ====================
add_action('wp_set_comment_status', function($comment_id, $comment_status) {
	static $processing = false; // 防无限循环
	if ($processing) return;
	$processing = true;

	if ($comment_status !== 'approve') {
		$processing = false;
		return;
	}

	$comment = get_comment($comment_id);
	$parent_id = $comment->comment_parent;
	if (!$parent_id) {
		$processing = false;
		return;
	}

	$parent_comment = get_comment($parent_id);

	// 用户回复自己的评论 → 不发邮件
	if ($comment->comment_author_email === $parent_comment->comment_author_email) {
		$processing = false;
		return;
	}
	// 被回复者是管理员 → 不通知
	$parent_is_admin = user_can(intval($parent_comment->user_id ?? 0), 'manage_options');
	if ($parent_is_admin) {
		$processing = false;
		return;
	}
	// 判断后台开关是否通知被回复用户
	if (weisay_option('wei_notify_user') == '1') {
		$email_send_user = generate_comment_email_message($comment, $parent_comment);
		$email_send_user = convert_smilies($email_send_user);
		send_comment_email($parent_comment->comment_author_email, '✨您在 [' . esc_html(get_option('blogname')) . '] 的评论有了新回复', $email_send_user);
	}
	$processing = false;
}, 10, 2);

// ==================== 后台测试邮件逻辑 ====================
add_action('wp_ajax_wei_send_test_mail', function() {
	check_ajax_referer('wei_send_test_mail_nonce', 'nonce'); // AJAX nonce 检查
	if (!current_user_can('manage_options')) {
		wp_send_json_error('没有权限发送测试邮件');
	}

	$smtp_host = weisay_option('wei_smtp_host');
	$smtp_user = weisay_option('wei_smtp_username');
	$smtp_pass = weisay_option('wei_smtp_password');
	if (!$smtp_host || !$smtp_user || !$smtp_pass) {
		wp_send_json_error('SMTP 信息不完整，请先填写 SMTP 服务器、邮箱和授权码/密码等信息。');
	}

	// 发送测试邮件
	$mail_sent = send_comment_email(
		get_option('admin_email'),
		'SMTP 测试邮件',
		'<h3>这是一封测试邮件</h3>
		 <p>恭喜！您的WordPress网站已成功配置SMTP邮件发送功能。</p>
		 <p>发送时间：' . date('Y-m-d H:i:s', current_time('timestamp')) . '</p>
		 <p>（来自Weisay Grace主题设置）</p>'
	);

	if ($mail_sent) {
		wp_send_json_success('测试邮件已发送成功，请检查管理员邮箱。');
	} else {
		wp_send_json_error('邮件发送失败，请检查 SMTP 配置是否正确。');
	}
});