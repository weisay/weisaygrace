<?php
/**
 * Ip2region 是一个离线 IP 数据管理框架和定位库，支持 IPv4 和 IPv6。此代码版本只支持 IPv4 。
 *
 * 官方社区：https://ip2region.net/
 */

require_once __DIR__ . '/xdb/Searcher.class.php';

use \ip2region\xdb\Util;
use \ip2region\xdb\Searcher;

// 全局 Searcher
global $ip2region_searcher_v4;
$ip2region_searcher_v4 = null;

// 初始化 IPv4 Searcher（向量索引模式）
function init_ip2region_vector($dbFile) {
	if (!file_exists($dbFile)) {
		error_log("IP数据库文件不存在: " . $dbFile);
		return null;
	}
	try {
		// 读取文件头，获取版本信息
		$header = Util::loadHeaderFromFile($dbFile);
		$version = Util::versionFromHeader($header);

		// 加载向量索引
		$vIndex = Util::loadVectorIndexFromFile($dbFile);

		// 创建 Searcher
		return Searcher::newWithVectorIndex($version, $dbFile, $vIndex);
	} catch (Exception $e) {
		error_log("IP数据库初始化失败: " . $e->getMessage());
		return null;
	}
}

// 获取 IPv4 searcher
function get_ip_searcher() {
	global $ip2region_searcher_v4;
	if ($ip2region_searcher_v4 === null) {
		$dbFile_v4 = __DIR__ . '/ipdata/ip2region_v4.xdb';
		$ip2region_searcher_v4 = init_ip2region_vector($dbFile_v4);
	}
	return $ip2region_searcher_v4;
}

// 检查内网 IPv4
function is_private_ip($ip) {
	return filter_var(
		$ip,
		FILTER_VALIDATE_IP,
		FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
	) === false;
}

// 主转换函数
function convertip($ip, $withIsp = false, $simpleMode = false) {
	if (!$ip) return '火星';
	if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
		return '火星';
	}

	// 内网IP
	if (is_private_ip($ip)) {
		return '内网IP';
	}

	// 获取 Searcher
	$searcher = get_ip_searcher();
	if ($searcher === null) {
		return '火星';
	}

	// 查询 IP
	try {
		$region = $searcher->search($ip);
	} catch (Exception $e) {
		return '火星';
	}

	$parts = explode('|', $region);
	$country = ($parts[0] !== '0') ? $parts[0] : '';
	$province = ($parts[1] !== '0') ? $parts[1] : '';
	$city = ($parts[2] !== '0') ? $parts[2] : '';
	$isp = ($parts[3] !== '0') ? $parts[3] : '';

	$resultParts = [];

	// 处理 "中国|0|0|ISP" 特殊情况
	if ($country === '中国' && $province === '' && $city === '') {
		$resultParts[] = '中国';
		if ($withIsp && $isp) {
			$resultParts[] = ' ' . $isp;
		}
		return implode('', $resultParts);
	}

	// 处理中国的专属逻辑
	if ($country === '中国') {
		// 直辖市列表
		$municipalities = ['北京', '上海', '天津', '重庆'];

		// 直辖市逻辑
		if (in_array($province, $municipalities) || in_array($city, $municipalities)) {
			$resultParts[] = $city ?: $province;
		} else {
			// 普通省份逻辑
			if ($province === $city) {
				$resultParts[] = $city;
			} else {
				if ($province) $resultParts[] = $province;
				if (!$simpleMode && $city) $resultParts[] = $city;
			}
		}
	} else {
		// 国外逻辑
		if ($country) $resultParts[] = $country;
		if ($province) $resultParts[] = $province;
		if (!$simpleMode && $city) $resultParts[] = $city;
	}

	// 可选显示网络ISP
	if ($withIsp && $isp) {
		$resultParts[] = ' ' . $isp;
	}

	// 兜底，防止结果为空
	if (empty($resultParts)) {
		return $country ?: '火星';
	}

	return implode('', $resultParts);
}

//简版 - 国内只显示省，国外显示国家 + 省
function convertipsimple($ip, $withIsp = false) {
	return convertip($ip, $withIsp, true);
}

?>