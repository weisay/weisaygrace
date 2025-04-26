<?php
function showReadableIpAddr($ipaddrParts) {
	$suffix = array('省', '市', '', '');
	if (trim($ipaddrParts[0]) == "中国") {
		array_shift($ipaddrParts);
		if (in_array(trim($ipaddrParts[0]), array('北京', '上海', '天津', '重庆')) === true) {
			array_shift($ipaddrParts);
			array_shift($suffix);
		} else {
			if (in_array(trim($ipaddrParts[0]), array('内蒙古', '广西', '西藏', '宁夏', '新疆')) === true) {
				$suffix[0] = '';
			} else if (in_array(trim($ipaddrParts[0]), array('香港', '澳门')) === true) {
				$suffix[0] = '';
			}
			if (count($ipaddrParts) > 1 && (str_ends_with($ipaddrParts[1], '自治州') || str_ends_with($ipaddrParts[1], '自治县') || str_ends_with($ipaddrParts[1], '地区') || str_ends_with($ipaddrParts[1], '盟') || str_ends_with($ipaddrParts[1], '旗') || str_ends_with($ipaddrParts[1], '林区') || str_ends_with($ipaddrParts[1], '半岛') || str_ends_with($ipaddrParts[1], '新界') || str_ends_with($ipaddrParts[1], '香港岛') || str_ends_with($ipaddrParts[1], '氹仔岛') || str_ends_with($ipaddrParts[1], '市') || str_ends_with($ipaddrParts[1], '县'))) {
				$suffix[1] = '';
			}
		}
		$ipaddrParts = array_map(fn($v1, $v2): string => !is_null($v1) ? trim($v1).$v2 : '', $ipaddrParts, $suffix);
	}
	return implode('', $ipaddrParts);
}

function parseIpDatabase($ip, $withNetworkInfo = false) {
	$dat_path = dirname(__FILE__).'/qqwry.dat';
	if(!$fd = @fopen($dat_path, 'rb')) {
		return $withNetworkInfo 
			? ['location' => 'IP date file not exists or access denied', 'network' => ''] 
			: 'IP date file not exists or access denied';
	}

	$ip = explode('.', $ip);
	$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
	
	$DataBegin = fread($fd, 4);
	$DataEnd = fread($fd, 4);
	$ipbegin = implode('', unpack('L', $DataBegin));
	if($ipbegin < 0) $ipbegin += pow(2, 32);
	$ipend = implode('', unpack('L', $DataEnd));
	if($ipend < 0) $ipend += pow(2, 32);

	$ipAllNum = ($ipend - $ipbegin) / 7 + 1;
	$BeginNum = 0;
	$EndNum = $ipAllNum;
	$ip1num = $ip2num = 0;

	while($ip1num > $ipNum || $ip2num < $ipNum) {
		$Middle = intval(($EndNum + $BeginNum) / 2);
		fseek($fd, $ipbegin + 7 * $Middle);
		$ipData1 = fread($fd, 4);
		if(strlen($ipData1) < 4) {
			fclose($fd);
			return $withNetworkInfo 
				? ['location' => 'System Error', 'network' => ''] 
				: 'System Error';
		}

		$ip1num = implode('', unpack('L', $ipData1));
		if($ip1num < 0) $ip1num += pow(2, 32);

		if($ip1num > $ipNum) {
			$EndNum = $Middle;
			continue;
		}

		$DataSeek = fread($fd, 3);
		if(strlen($DataSeek) < 3) {
			fclose($fd);
			return $withNetworkInfo 
				? ['location' => 'System Error', 'network' => ''] 
				: 'System Error';
		}

		$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
		fseek($fd, $DataSeek);
		$ipData2 = fread($fd, 4);
		if(strlen($ipData2) < 4) {
			fclose($fd);
			return $withNetworkInfo 
				? ['location' => 'System Error', 'network' => ''] 
				: 'System Error';
		}

		$ip2num = implode('', unpack('L', $ipData2));
		if($ip2num < 0) $ip2num += pow(2, 32);
		
		if($ip2num < $ipNum) {
			if($Middle == $BeginNum) {
				fclose($fd);
				return $withNetworkInfo 
					? ['location' => 'Unknown', 'network' => ''] 
					: 'Unknown';
			}
			$BeginNum = $Middle;
		}
	}

	$ipFlag = fread($fd, 1);
	$ipAddr1 = $ipAddr2 = '';
	
	if($ipFlag == chr(1)) {
		$ipSeek = fread($fd, 3);
		if(strlen($ipSeek) < 3) {
			fclose($fd);
			return $withNetworkInfo 
				? ['location' => 'System Error', 'network' => ''] 
				: 'System Error';
		}
		$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
		fseek($fd, $ipSeek);
		$ipFlag = fread($fd, 1);
	}

	if($ipFlag == chr(2)) {
		$AddrSeek = fread($fd, 3);
		if(strlen($AddrSeek) < 3) {
			fclose($fd);
			return $withNetworkInfo 
				? ['location' => 'System Error', 'network' => ''] 
				: 'System Error';
		}
		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(2)) {
			$AddrSeek2 = fread($fd, 3);
			if(strlen($AddrSeek2) < 3) {
				fclose($fd);
				return $withNetworkInfo 
					? ['location' => 'System Error', 'network' => ''] 
					: 'System Error';
			}
			$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
			fseek($fd, $AddrSeek2);
		} else {
			fseek($fd, -1, SEEK_CUR);
		}
		while(($char = fread($fd, 1)) != chr(0))
			$ipAddr2 .= $char;
		$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
		fseek($fd, $AddrSeek);
		while(($char = fread($fd, 1)) != chr(0))
			$ipAddr1 .= $char;
	} else {
		fseek($fd, -1, SEEK_CUR);
		while(($char = fread($fd, 1)) != chr(0))
			$ipAddr1 .= $char;

		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(2)) {
			$AddrSeek2 = fread($fd, 3);
			if(strlen($AddrSeek2) < 3) {
				fclose($fd);
				return $withNetworkInfo 
					? ['location' => 'System Error', 'network' => ''] 
					: 'System Error';
			}
			$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
			fseek($fd, $AddrSeek2);
		} else {
			fseek($fd, -1, SEEK_CUR);
		}
		while(($char = fread($fd, 1)) != chr(0)) {
			$ipAddr2 .= $char;
		}
	}
	fclose($fd);

	// 处理地理位置信息
	$location = "$ipAddr1";
	$location = preg_replace('/CZ88.Net/is', '', $location);
	$location = preg_replace('/^\s*/is', '', $location);
	$location = preg_replace('/\s*$/is', '', $location);
	if(preg_match('/http/i', $location) || $location == '') {
		$location = 'Unknown';
	}
	$location = iconv('gbk', 'utf-8', $location);

	// 处理网络信息
	$network = preg_replace('/CZ88.Net/is', '', $ipAddr2);
	$network = preg_replace('/^\s*/is', '', $network);
	$network = preg_replace('/\s*$/is', '', $network);
	$network = iconv('gbk', 'utf-8', $network);
	if(preg_match('/http/i', $network) || $network == '') {
		$network = '';
	}

	if ($withNetworkInfo) {
		return ['location' => $location, 'network' => $network];
	}
	return $location;
}

function convertip($ip, $withNetworkInfo = false) {
	$result = parseIpDatabase($ip, $withNetworkInfo || strpos($ip, 'showNetwork') !== false);
	
	if (is_array($result)) {
		$ipaddr = $result['location'];
		$network = $result['network'];
	} else {
		$ipaddr = $result;
		$network = '';
	}

	if (strpos($ipaddr, '–') !== false) {
		$ipaddrParts = explode("–", $ipaddr);
		$ipaddr = showReadableIpAddr($ipaddrParts);
	}

	if ($withNetworkInfo && !empty($network)) {
		$ipaddr .= ' ' . $network;
	}

	if ($ipaddr == '  ') {
		$ipaddr = '火星来客';
	}
	return $ipaddr;
}

function convertipsimple($ip, $withNetworkInfo = false) {
	$result = parseIpDatabase($ip, $withNetworkInfo || strpos($ip, 'showNetwork') !== false);
	
	if (is_array($result)) {
		$ipaddr = $result['location'];
		$network = $result['network'];
	} else {
		$ipaddr = $result;
		$network = '';
	}

	if (strpos($ipaddr, '–') !== false) {
		$ipaddrParts = explode("–", $ipaddr);
		
		if (trim($ipaddrParts[0]) == "中国") {
			array_shift($ipaddrParts);
			
			$municipalities = array('北京', '上海', '天津', '重庆');
			if (in_array(trim($ipaddrParts[0]), $municipalities)) {
				$ipaddr = trim($ipaddrParts[0]).'市';
			} 
			else if (in_array(trim($ipaddrParts[0]), array('内蒙古', '广西', '西藏', '宁夏', '新疆', '香港', '澳门'))) {
				$ipaddr = trim($ipaddrParts[0]);
			} else {
				$ipaddr = trim($ipaddrParts[0]).'省';
			}
		} 
		else {
			$ipaddr = trim($ipaddrParts[0]);
		}
	}

	if ($withNetworkInfo && !empty($network)) {
		$ipaddr .= ' ' . $network;
	}
	
	if ($ipaddr == '  ') {
		$ipaddr = '火星来客';
	}
	return $ipaddr;
}
?>