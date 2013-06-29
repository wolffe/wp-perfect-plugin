<?php
$alexa_backlink = 0;
$alexa_reach = 0;

function StrToNum($Str, $Check, $Magic) {
	$Int32Unit = 4294967296;

	$length = strlen($Str);
	for($i = 0; $i < $length; $i++) { 
		$Check *= $Magic;      
		if($Check >= $Int32Unit) { 
			$Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit)); 
			$Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check; 
		}
		$Check += ord($Str{$i}); 
	}
	return $Check; 
}
function HashURL($String) { 
	$Check1 = StrToNum($String, 0x1505, 0x21); 
	$Check2 = StrToNum($String, 0, 0x1003F); 

	$Check1 >>= 2;      
	$Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F); 
	$Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF); 
	$Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);    

	$T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F ); 
	$T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 ); 

	return ($T1 | $T2); 
}
function CheckHash($Hashnum) {
	$CheckByte = 0; 
	$Flag = 0; 

	$HashStr = sprintf('%u', $Hashnum);
	$length = strlen($HashStr); 

	for($i = $length - 1;  $i >= 0;  $i --) { 
		$Re = $HashStr{$i}; 
		if(1 === ($Flag % 2)) {              
			$Re += $Re;      
			$Re = (int)($Re / 10) + ($Re % 10); 
		} 
		$CheckByte += $Re; 
		$Flag ++;    
	} 

	$CheckByte %= 10; 
	if(0 !== $CheckByte) { 
		$CheckByte = 10 - $CheckByte; 
		if(1 === ($Flag % 2) ) { 
			if(1 === ($CheckByte % 2)) { 
				$CheckByte += 9; 
			} 
			$CheckByte >>= 1; 
		} 
	} 

	return '7' . $CheckByte . $HashStr; 
} 

function getpagerank($url) { 
	$query="http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".CheckHash(HashURL($url)). "&features=Rank&q=info:".$url."&num=100&filter=0"; 
	$data=file_get_contents_curl($query); 
	$pos = strpos($data, "Rank_"); 
	if($pos === false){} else{ 
		$pagerank = substr($data, $pos + 9); 
		return $pagerank; 
	} 
} 
function get_alexa_popularity($url) {
	global $alexa_backlink, $alexa_reach; 
	$alexaxml = "http://xml.alexa.com/data?cli=10&dat=nsa&url=".$url; 

	$xml_parser = xml_parser_create(); 
	$data=file_get_contents_curl($alexaxml); 
	xml_parse_into_struct($xml_parser, $data, $vals, $index); 
	xml_parser_free($xml_parser); 

	$index_popularity = $index['POPULARITY'][0]; 
	$index_reach = $index['REACH'][0]; 
	$index_linksin = $index['LINKSIN'][0]; 
	$alexarank = $vals[$index_popularity]['attributes']['TEXT']; 
	$alexa_backlink = $vals[$index_linksin]['attributes']['NUM']; 
	$alexa_reach = $vals[$index_reach]['attributes']['RANK']; 

	return $alexarank; 
} 
function alexa_backlink($url) {
	global $alexa_backlink; 
	if($alexa_backlink!=0) { 
		return $alexa_backlink; 
	} else { 
		$rank=get_alexa_popularity($url); 
		return $alexa_backlink; 
	} 
} 
if(!function_exists('file_get_contents_curl')) {
	function file_get_contents_curl($url) { 
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url); 
		$data = curl_exec($ch); 
		curl_close($ch); 

		return $data; 
	}
}
function googlebot_lastaccess($domain_name) {
	$request = 'http://webcache.googleusercontent.com/search?hl=en&q=cache:'.$domain_name.'&btnG=Google+Search&meta=';
	$data = getPageData($request);
	$spl=explode("as it appeared on",$data);
	$spl2=explode(".<br>",$spl[1]);
	$value=trim($spl2[0]);
	if(strlen($value)==0)
		return(0);
	else
		return($value);
} 
function getPageData($url) {
	if(function_exists('curl_init')) {
		$ch = curl_init($url); // initialize curl with given url
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // add useragent
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
		if((ini_get('open_basedir') == '') && (ini_get('safe_mode') == 'Off')) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // max. seconds to execute
		curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
		return @curl_exec($ch);
	}
	else {
		return @file_get_contents($url);
	}
}

function wp_seo_rank_widget_admin_function() {
	$w3p_tracked_site = get_option('siteurl');

	$url = $w3p_tracked_site;
	$content = googlebot_lastaccess($url);
	$date = substr($content , 0, strpos($content, 'GMT') + strlen('GMT'));

	echo '<p>';
		echo 'Your site has a PageRank&trade; of <strong>' . getpagerank($url) . '</strong>. Google bot last visited your site on <strong>' . $date . '</strong>.<br>';
		echo 'Your site is ranked <strong><a target="_blank" href="http://www.alexa.com/siteinfo/' . $url . '">' . get_alexa_popularity($url) . '</a></strong> in Alexa and has <strong>' . alexa_backlink($url) . '</strong> backlinks <em>(based on Alexa)</em>.';
	echo '</p>';
}
