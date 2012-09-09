<?php
//global variable 
$alexa_backlink=0; 
$alexa_reach=0; 

//--> for google pagerank 
function StrToNum($Str, $Check, $Magic) 
{ 
    $Int32Unit = 4294967296;  // 2^32 

    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++) { 
        $Check *= $Magic;      
        //If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31), 
        //  the result of converting to integer is undefined 
        //  refer to http://www.php.net/manual/en/language.types.integer.php 
        if ($Check >= $Int32Unit) { 
            $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit)); 
            //if the check less than -2^31 
            $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check; 
        } 
        $Check += ord($Str{$i}); 
    } 
    return $Check; 
} 

//--> for google pagerank 
/* 
* Genearate a hash for a url 
*/ 
function HashURL($String) 
{ 
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

//--> for google pagerank 
/* 
* genearate a checksum for the hash string 
*/ 
function CheckHash($Hashnum) 
{ 
    $CheckByte = 0; 
    $Flag = 0; 

    $HashStr = sprintf('%u', $Hashnum) ; 
    $length = strlen($HashStr); 
    
    for ($i = $length - 1;  $i >= 0;  $i --) { 
        $Re = $HashStr{$i}; 
        if (1 === ($Flag % 2)) {              
            $Re += $Re;      
            $Re = (int)($Re / 10) + ($Re % 10); 
        } 
        $CheckByte += $Re; 
        $Flag ++;    
    } 

    $CheckByte %= 10; 
    if (0 !== $CheckByte) { 
        $CheckByte = 10 - $CheckByte; 
        if (1 === ($Flag % 2) ) { 
            if (1 === ($CheckByte % 2)) { 
                $CheckByte += 9; 
            } 
            $CheckByte >>= 1; 
        } 
    } 

    return '7'.$CheckByte.$HashStr; 
} 

//get google pagerank 
function getpagerank($url) { 
    $query="http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".CheckHash(HashURL($url)). "&features=Rank&q=info:".$url."&num=100&filter=0"; 
    $data=file_get_contents_curl($query); 
    //print_r($data); 
    $pos = strpos($data, "Rank_"); 
    if($pos === false){} else{ 
        $pagerank = substr($data, $pos + 9); 
        return $pagerank; 
    } 
} 


//get alexa popularity 
function get_alexa_popularity($url) 
{    
global $alexa_backlink, $alexa_reach; 
    $alexaxml = "http://xml.alexa.com/data?cli=10&dat=nsa&url=".$url; 
    
    $xml_parser = xml_parser_create(); 
    /* 
    $fp = fopen($alexaxml, "r") or die("Error: Reading XML data."); 
    $data = ""; 
    while (!feof($fp)) { 
        $data .= fread($fp, 8192); 
        //echo "masuk while<br />"; 
    } 
    fclose($fp); 
    */ 
    $data=file_get_contents_curl($alexaxml); 
    xml_parse_into_struct($xml_parser, $data, $vals, $index); 
    xml_parser_free($xml_parser); 
    
    //print_r($vals); 
    //echo "<br />"; 
    //print_r($index); 
    
    $index_popularity = $index['POPULARITY'][0]; 
    $index_reach = $index['REACH'][0]; 
    $index_linksin = $index['LINKSIN'][0]; 
    //echo $index_popularity."<br />"; 
    //print_r($vals[$index_popularity]); 
    $alexarank = $vals[$index_popularity]['attributes']['TEXT']; 
    $alexa_backlink = $vals[$index_linksin]['attributes']['NUM']; 
    $alexa_reach = $vals[$index_reach]['attributes']['RANK']; 
    
    return $alexarank; 
} 

//get alexa backlink 
function alexa_backlink($url) 
{ 
    global $alexa_backlink; 
    if ($alexa_backlink!=0) 
    { 
        return $alexa_backlink; 
    } else { 
        $rank=get_alexa_popularity($url); 
        return $alexa_backlink; 
    } 
} 

//get alexa reach rank 
function alexa_reach_rank($url) 
{ 
    global $alexa_reach; 
    if ($alexa_reach!=0) 
    { 
        return $alexa_reach; 
    } else { 
        $rank=get_alexa_popularity($url); 
        return $alexa_reach; 
    } 
} 




function file_get_contents_curl($url) { 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser. 
    curl_setopt($ch, CURLOPT_URL, $url); 
    $data = curl_exec($ch); 
    curl_close($ch); 

    return $data; 
}







//get googlebot last access
function googlebot_lastaccess($domain_name)
{
    $request = 'http://webcache.googleusercontent.com/search?hl=en&q=cache:'.$domain_name.'&btnG=Google+Search&meta=';
    $data = getPageData($request);
    $spl=explode("as it appeared on",$data);
   //echo "<pre>".$spl[0]."</pre>";
    $spl2=explode(".<br>",$spl[1]);
    $value=trim($spl2[0]);
   //echo "<pre>".$spl2[0]."</pre>";
    if(strlen($value)==0)
    {
        return(0);
    }
    else
    {
        return($value);
    }


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
	global $wpdb;
	$data = get_option('wp_seo_rank');

	if(isset($_POST['wp_seo_rank_save'])) {
		$data['feedburner'] = attribute_escape($_POST['wp_seo_rank_feedburner']);
		$data['ValLastUpdate'] = date('Y-m-d');
		update_option('wp_seo_rank', $data);
	}

	$w3p_tracked_site = get_option('siteurl');
	?>

	<?php
	$url = $w3p_tracked_site;
	$content = googlebot_lastaccess($url);
	$date = substr($content , 0, strpos($content, 'GMT') + strlen('GMT'));

	echo '<p>';
		echo '<strong>'.$w3p_tracked_site.'</strong>';
	echo '</p>';
	echo '<p>';
		echo 'Your site has a PageRank&trade; of <strong>'.getpagerank($url).'</strong>. Google bot last visited your site on <strong>'.$date.'</strong> and detected <a target="_blank" href="http://www.google.com/search?oe=utf8&ie=utf8&source=uds&start=0&filter=0&hl=en&q=link:'.site_url().'">'.number_format(get_backlinks_google(site_url())).'</a> backlinks.<br />';
		echo 'Your site is ranked <strong><a target="_blank" href="http://www.alexa.com/siteinfo/'.$url.'">'.get_alexa_popularity($url).'</a></strong> in Alexa and has <strong>'.alexa_backlink($url).'</strong> backlinks <em>(based on Alexa)</em>.';
	echo '</p>';
	echo '<p>';
		echo 'Google Feedburner has <a target="_blank" href="http://feeds.feedburner.com/'.get_option('w3p_feedburner').'">'.number_format(getFeedBurner(get_option('w3p_feedburner'))).'</a> subscribers.';
	echo '</p>';
}

function get_backlinks_google($url) {
	$content = file_get_contents('http://ajax.googleapis.com/ajax/services/search/web?v=1.0&filter=0&key=ABQIAAAA6f5Achoodo5s2Q2049vn6BSIkO30j4gnxwlOBxQkFXOonq3PsBQ0hUYBhAxwx8DYL03zbFQWDSv_nA&q=link:'.urlencode($url));
	$data = json_decode($content);
	return intval($data->responseData->cursor->estimatedResultCount);
}
function getFeedBurner($user) {
	$xml = file_get_contents("https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=http://feeds.feedburner.com/$user");
	return get_match('/circulation="(.*)"/isU',$xml);
}
function get_match($regex,$content) {
	preg_match($regex,$content,$matches);
	return $matches[1];
}


// SEO TRACKER PAGE FUNCTION
function w3p_seo_options() {
	$hidden_field_name = 'wpfc_submit_hidden';
	$w3p_email_field_name = 'w3p_email';

	// read in existing option value from database
    $option_value_w3p_email = get_option('w3p_email');

    // See if the user has posted us some information // if they did, this hidden field will be set to 'Y'
	if(isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
		$option_value_w3p_email = $_POST[$w3p_email_field_name];

		update_option('w3p_email', $option_value_w3p_email);
		?>
		<div class="updated"><p><strong>Settings saved.</strong></p></div>
		<?php
	}

	$w3p_email = get_option('w3p_email');
	$w3p_tracked_site = get_option('siteurl');
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>SEO Tracker</h2>

		<p><a href="http://www.alexa.com/siteinfo/<?php echo $w3p_tracked_site;?>"><script type="text/javascript" src="http://xslt.alexa.com/site_stats/js/s/c?url=<?php echo $w3p_tracked_site;?>"></script></a></p>

		<?php wp_seo_rank_widget_admin_function();?>

		<h3>Quick Links</h3>
		<ul>
			<li><a href="http://www.google.com/webmasters/" rel="external">Google Webmaster Tools</a></li>
			<li><a href="http://www.google.com/analytics/" rel="external">Google Analytics</a></li>
			<li><a href="http://www.bing.com/toolbox/webmasters/" rel="external">Bing Webmaster Tools</a></li>
			<li><a href="http://www.alexa.com/siteowners" rel="external">Alexa Site Tools</a></li>
		</ul>
	</div>
<?php }?>