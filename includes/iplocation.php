<?php

// 清洗 IP 字符串内容，统一处理 CZ88.Net、空白、编码等
function cleanIpText(string $text): string {
	$text = preg_replace('/CZ88\.Net/is', '', $text);
	$text = trim($text);
	$text = iconv('gbk', 'utf-8//IGNORE', $text);
	return (preg_match('/http/i', $text) || $text === '') ? '' : $text;
}

// IPv4格式校验
function isValidIp(string $ip): bool {
	return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
}

// 地理位置信息格式化（加上省市后缀等）
function showReadableIpAddr(array $ipaddrParts): string {
	$suffix = ['省', '市', '', ''];

	if (trim($ipaddrParts[0]) === "中国") {
		array_shift($ipaddrParts);

		$province = trim($ipaddrParts[0] ?? '');
		$municipalities = ['北京', '上海', '天津', '重庆'];
		$autonomousRegions = ['内蒙古', '广西', '西藏', '宁夏', '新疆']; 
		$specialRegions = ['香港', '澳门']; 
		$nonCitySuffixes = ['自治州', '自治县', '地区', '盟', '旗', '林区', '半岛', '新界', '香港岛', '氹仔岛', '市', '县'];

		if (in_array($province, $municipalities, true)) {
			array_shift($ipaddrParts);
			array_shift($suffix);
		} else {
			if (in_array($province, $autonomousRegions, true)) {
				$suffix[0] = '';	// 自治区
			} elseif (in_array($province, $specialRegions, true)) {
				$suffix[0] = '';	// 特别行政区
			}
			$second = $ipaddrParts[1] ?? '';
			if ($second === '' || preg_match('/(' . implode('|', $nonCitySuffixes) . ')$/u', $second)) {
				$suffix[1] = '';
			}
		}

		$ipaddrParts = array_map(
			function($v1, $v2) {
				return $v1 !== null ? trim($v1) . $v2 : '';
			},
			$ipaddrParts,
			$suffix
		);
	}

	return implode('', $ipaddrParts);
}

// 解析二进制偏移（3字节 → 4字节 LittleEndian）
function read3BytesOffset($fd) {
	$data = fread($fd, 3);
	if (strlen($data) < 3) return false;
	return unpack('V', $data . chr(0))[1];
}

// 查询 qqwry.dat 数据库并返回结果
function parseIpDatabase($ip, $withNetworkInfo = false) {
	$dat_path = __DIR__ . '/qqwry.dat';
	if (!$fd = @fopen($dat_path, 'rb')) {
		return $withNetworkInfo
			? ['location' => 'IP数据库不可用', 'network' => '']
			: 'IP数据库不可用';
	}

	$ipParts = explode('.', $ip);
	if (count($ipParts) !== 4) {
		fclose($fd);
		return $withNetworkInfo ? ['location' => 'Invalid IP format', 'network' => ''] : 'Invalid IP format';
	}

	$ipNum = $ipParts[0] * 16777216 + $ipParts[1] * 65536 + $ipParts[2] * 256 + $ipParts[3];

	$DataBegin = fread($fd, 4);
	$DataEnd = fread($fd, 4);
	$ipbegin = unpack('V', $DataBegin)[1];
	$ipend   = unpack('V', $DataEnd)[1];

	$ipAllNum = ($ipend - $ipbegin) / 7 + 1;
	$BeginNum = 0;
	$EndNum = $ipAllNum;
	$ip1num = $ip2num = 0;

	while ($ip1num > $ipNum || $ip2num < $ipNum) {
		$Middle = intval(($EndNum + $BeginNum) / 2);
		fseek($fd, $ipbegin + 7 * $Middle);
		$ip1num = unpack('V', fread($fd, 4))[1];
		$seekOffset = read3BytesOffset($fd);
		fseek($fd, $seekOffset);
		$ip2num = unpack('V', fread($fd, 4))[1];

		if ($ip1num > $ipNum) {
			$EndNum = $Middle;
		} elseif ($ip2num < $ipNum) {
			if ($Middle == $BeginNum) break;
			$BeginNum = $Middle;
		}
	}

	$ipFlag = fread($fd, 1);
	$ipAddr1 = $ipAddr2 = '';

	if ($ipFlag === chr(1)) {
		$ipSeek = read3BytesOffset($fd);
		fseek($fd, $ipSeek);
		$ipFlag = fread($fd, 1);
	}

	if ($ipFlag === chr(2)) {
		$AddrSeek = read3BytesOffset($fd);
		$ipFlag = fread($fd, 1);
		if ($ipFlag === chr(2)) {
			$AddrSeek2 = read3BytesOffset($fd);
			fseek($fd, $AddrSeek2);
		} else {
			fseek($fd, -1, SEEK_CUR);
		}
		while (($char = fread($fd, 1)) !== chr(0)) $ipAddr2 .= $char;
		fseek($fd, $AddrSeek);
		while (($char = fread($fd, 1)) !== chr(0)) $ipAddr1 .= $char;
	} else {
		fseek($fd, -1, SEEK_CUR);
		while (($char = fread($fd, 1)) !== chr(0)) $ipAddr1 .= $char;

		$ipFlag = fread($fd, 1);
		if ($ipFlag === chr(2)) {
			$AddrSeek2 = read3BytesOffset($fd);
			fseek($fd, $AddrSeek2);
		} else {
			fseek($fd, -1, SEEK_CUR);
		}
		while (($char = fread($fd, 1)) !== chr(0)) $ipAddr2 .= $char;
	}

	fclose($fd);

	$location = cleanIpText($ipAddr1);
	$network  = cleanIpText($ipAddr2);

	if ($withNetworkInfo) {
		return ['location' => $location ?: 'Unknown', 'network' => $network];
	}
	return $location ?: 'Unknown';
}

// 主函数：对外接口，格式化 IP 地址地理信息
function convertip($ip, $withNetworkInfo = false) {
	if (!isValidIp($ip)) return '火星';

	$result = parseIpDatabase($ip, $withNetworkInfo);

	if (is_array($result)) {
		$ipaddr = $result['location'];
		$network = $result['network'];
	} else {
		$ipaddr = $result;
		$network = '';
	}

	if (strpos($ipaddr, '–') !== false) {
		$ipaddrParts = explode('–', $ipaddr);
		$ipaddr = showReadableIpAddr($ipaddrParts);
	}

	if ($withNetworkInfo && $network !== '') {
		$ipaddr .= ' ' . $network;
	}

	return trim($ipaddr) === '' ? '火星来客' : $ipaddr;
}

// 简化版 IP 转换，仅返回省份级别的地址
function convertipsimple($ip, $withNetworkInfo = false) {
	if (!isValidIp($ip)) return '火星';

	$result = parseIpDatabase($ip, $withNetworkInfo);

	if (is_array($result)) {
		$ipaddr = $result['location'];
		$network = $result['network'];
	} else {
		$ipaddr = $result;
		$network = '';
	}

	if (strpos($ipaddr, '–') !== false) {
		$ipaddrParts = explode('–', $ipaddr);

		if (trim($ipaddrParts[0]) === "中国") {
			array_shift($ipaddrParts);

			$province = trim($ipaddrParts[0] ?? '');
			$municipalities = ['北京', '上海', '天津', '重庆'];
			$autonomousRegions = ['内蒙古', '广西', '西藏', '宁夏', '新疆'];
			$specialRegions = ['香港', '澳门'];

			if (in_array($province, $municipalities, true)) {
				$ipaddr = $province . '市';
			} elseif (in_array($province, $autonomousRegions, true)) {
				$ipaddr = $province . '';	// 自治区
			} elseif (in_array($province, $specialRegions, true)) {
				$ipaddr = $province . '';	// 特别行政区
			} else {
				$ipaddr = $province . '省';
			}
		} else {
			$ipaddr = trim($ipaddrParts[0]);
		}
	}

	if ($withNetworkInfo && $network !== '') {
		$ipaddr .= ' ' . $network;
	}

	return trim($ipaddr) === '' ? '火星来客' : $ipaddr;
}
?>