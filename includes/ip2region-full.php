<?php
/**
 * Ip2region 是一个离线 IP 数据管理框架和定位库，支持 IPv4 和 IPv6。此代码版本支持 IPv4 和 IPv6。
 *
 * 官方社区：https://ip2region.net/
 */
 
require_once __DIR__ . '/xdb/Searcher.class.php';

use \ip2region\xdb\Util;
use \ip2region\xdb\Searcher;

//初始化，使用向量索引
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

// 全局 Searcher，显式初始化为 null
global $ip2region_searcher_v4, $ip2region_searcher_v6;
$ip2region_searcher_v4 = $ip2region_searcher_v4 ?? null;
$ip2region_searcher_v6 = $ip2region_searcher_v6 ?? null;

//获取 IPv4 或 IPv6 Searcher
function get_ip_searcher($ip) {
	global $ip2region_searcher_v4, $ip2region_searcher_v6;

	// 判断 ip 类型
	$isIpv6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	$isIpv4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

	// 尝试加载 IPv6 searcher
	if ($isIpv6) {
		if ($ip2region_searcher_v6 === null) {
			$dbFile_v6 = __DIR__ . '/ipdata/ip2region_v6.xdb';
			$ip2region_searcher_v6 = init_ip2region_vector($dbFile_v6);
		}
		if ($ip2region_searcher_v6 !== null) {
			return $ip2region_searcher_v6;
		}
		// 如果 IPv6 DB 不可用，继续尝试加载 IPv4（fallback降级）
	}

	// IPv4 路径（或者作为fallback降级）
	if ($ip2region_searcher_v4 === null) {
		$dbFile_v4 = __DIR__ . '/ipdata/ip2region_v4.xdb';
		$ip2region_searcher_v4 = init_ip2region_vector($dbFile_v4);
	}

	return $ip2region_searcher_v4;
}

//判断 IP 类型
function get_ip_type($ip) {
	if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
		return 'ipv4';
	} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
		return 'ipv6';
	}
	return false;
}

//判断内网IP并返回显示文本
function is_private_ip($ip) {
	$ip_type = get_ip_type($ip);

	if ($ip_type === 'ipv4') {
		if (filter_var(
			$ip,
			FILTER_VALIDATE_IP,
			FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
		) === false) {
			return '内网IP';
		}
	} elseif ($ip_type === 'ipv6') {
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false) {
			return '内网IP';
		}
		if ($ip === '::1') {
			return '内网IP';
		}
		if (preg_match('/^fe80:/i', $ip)) {
			return '内网IP';
		}
	}
	return false;
}

//IP转换函数
function convertip($ip, $withIsp = false, $simpleMode = false) {
	if (!$ip) return '火星';

	// 检查内网IP
	$private_result = is_private_ip($ip);
	if ($private_result !== false) {
		return $private_result;
	}

	// 获取 searcher（延迟加载、并可降级）
	$searcher = get_ip_searcher($ip);
	if ($searcher === null) {
		return '火星'; // 数据库不存在或加载失败
	}

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