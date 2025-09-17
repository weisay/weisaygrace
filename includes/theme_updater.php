<?php
/**
 * Weisay Grace 主题后台更新提醒
 */

if (!defined('ABSPATH')) {
	exit; // 防止直接访问
}

class WeisayGrace_Theme_Updater {

	private $theme_slug;
	private $remote_url = 'https://img.weisay.com/theme/weisaygrace/theme.json';
	private $theme_name = 'Weisay Grace';
	private $author_name = 'Weisay';
	private $author_url = 'https://www.weisay.com';
	private $homepage_url = 'https://www.weisay.com/blog/wordpress-theme-weisay-grace.html';
	private $cache_key = 'weisaygrace_theme_update_info';
	private $cache_time = 12 * HOUR_IN_SECONDS; // 12小时缓存

	public function __construct() {
		$this->theme_slug = basename(get_template_directory());

		// 仪表盘 → 更新
		add_filter('pre_set_site_transient_update_themes', [$this, 'check_for_update']);

		// 外观 → 主题 页面访问时
		add_action('load-themes.php', [$this, 'maybe_check_update_for_theme_page']);

		// 主题详情弹窗
		add_filter('themes_api', [$this, 'update_details'], 10, 3);

		// 切换/启用主题时立即检查
		add_action('after_switch_theme', [$this, 'force_check_update']);
	}

	// 检查是否有更新
	public function check_for_update($transient) {
		if (empty($transient)) {
			$transient = new stdClass();
			$transient->response = [];
			$transient->checked = [];
		}

		// 当前主题版本
		$current_version = wp_get_theme($this->theme_slug)->get('Version');
		$transient->checked[$this->theme_slug] = $current_version;

		$remote_info = $this->get_remote_info();

		if ($remote_info && version_compare($current_version, $remote_info->version, '<')) {
			$transient->response[$this->theme_slug] = [
				'theme' => $this->theme_slug,
				'new_version' => $remote_info->version,
				'url' => $remote_info->details_url ?? $this->homepage_url,
				'package' => $remote_info->download_url ?? '',
				'requires' => $remote_info->requires ?? '',
				'requires_php' => $remote_info->requires_php ?? ''
			];
		}

		return $transient;
	}

	// 主题详情弹窗
	public function update_details($result, $action, $args) {
		if ($action !== 'theme_information' || !isset($args->slug) || $args->slug !== $this->theme_slug) {
			return $result;
		}

		$remote_info = $this->get_remote_info();
		if (!$remote_info) {
			return $result;
		}

		return (object)[
			'name' => $this->theme_name,
			'slug' => $this->theme_slug,
			'version' => $remote_info->version,
			'author' => sprintf('<a href="%s">%s</a>', esc_url($this->author_url), esc_html($this->author_name)),
			'author_profile' => $this->author_url,
			'homepage' => $this->homepage_url,
			'download_link' => $remote_info->download_url ?? '',
			'requires' => $remote_info->requires ?? '',
			'requires_php' => $remote_info->requires_php ?? '',
			'sections' => [
				'description' => sprintf(
					'这是 %s 主题的更新说明，详见 <a href="%s" target="_blank">更新日志</a>。',
					$this->theme_name,
					esc_url($remote_info->details_url ?? $this->homepage_url)
				),
			],
			'banners' => [
				'low' => $remote_info->banner_low ?? '',
				'high' => $remote_info->banner_high ?? ''
			]
		];
	}

	// 获取远程更新信息
	private function get_remote_info() {
		$cached = get_transient($this->cache_key);
		if ($cached !== false) {
			return $cached;
		}

		$response = wp_remote_get($this->remote_url, [
			'timeout' => 20,
			'headers' => ['Accept' => 'application/json']
		]);

		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			// 如果请求失败，设置短时间缓存避免频繁请求
			set_transient($this->cache_key, false, 10 * MINUTE_IN_SECONDS);
			return false;
		}

		$body = wp_remote_retrieve_body($response);
		$remote_info = json_decode($body);

		if (!$remote_info || !isset($remote_info->version)) {
			set_transient($this->cache_key, false, 10 * MINUTE_IN_SECONDS);
			return false;
		}

		set_transient($this->cache_key, $remote_info, $this->cache_time);
		return $remote_info;
	}

	// 强制刷新（切换/启用主题时）
	public function force_check_update() {
		delete_transient($this->cache_key);
		delete_site_transient('update_themes');
		wp_update_themes();
	}

	// 主题列表页面访问时，判断缓存是否存在，不存在才生成
	public function maybe_check_update_for_theme_page() {
		// 获取远程主题信息缓存
		$remote_info = $this->get_remote_info();
		if ($remote_info) {
		delete_site_transient('update_themes');
		wp_update_themes();
		}
	}
}

// 初始化
new WeisayGrace_Theme_Updater();

?>