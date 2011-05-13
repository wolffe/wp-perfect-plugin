<?php
function wp_seo_rank_widget_admin_function() {
	global $wpdb;
	$data = get_option('wp_seo_rank');

	if(isset($_POST['wp_seo_rank_save'])) {
		$data['feedburner'] = attribute_escape($_POST['wp_seo_rank_feedburner']);
		$data['twitter'] = attribute_escape($_POST['wp_seo_rank_twitter']);
		$data['youtube'] = attribute_escape($_POST['wp_seo_rank_youtube']);
		$data['autoupdate'] = attribute_escape($_POST['wp_seo_rank_autoupdate']);
		update_option('wp_seo_rank', $data);
	}
	if(isset($_POST['wp_seo_rank_update'])) {
		update_seo(1);
	}

	$data = get_option('wp_seo_rank');
	$w3p_tracked_site = get_option('siteurl');
	?>
	<table class="widefat">
		<thead>
			<tr>
				<th>Alexa Rank</th>
				<th>Google PageRank</th>
				<th>Google Backlinks</th>
				<th>Yahoo Backlinks</th>
				<th>Feedburner Subscribers</th>
				<th>Twitter Followers</th>
				<th>Youtube Subscribers</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><a target="_blank" href="http://www.alexa.com/siteinfo/<?php echo site_url(); ?>"><?php echo number_format($data['ValAlexaRank']); ?></a></td>
				<td><strong><?php echo getprSeo($w3p_tracked_site);?></strong></td>
				<td><a target="_blank" href="http://www.google.com/search?oe=utf8&ie=utf8&source=uds&start=0&filter=0&hl=en&q=link:<?php echo site_url(); ?>"><?php echo number_format($data['ValBacklinksGoogle']); ?></a></td>
				<td><a target="_blank" href="http://siteexplorer.search.yahoo.com/search;_ylt=A0oG7zbdV5ZMIt8AdFddhMkF?p=<?php echo site_url(); ?>&y=Explore+URL&fr=sfp"><?php echo number_format($data['ValBacklinksYahoo']); ?></a></td>
				<td><a target="_Blank" href="http://feeds.feedburner.com/<?php echo $data['feedburner']; ?>"><?php echo number_format($data['ValFeedBurner']); ?></a></td>
				<td><a target="_blank" href="http://twitter.com/<?php echo $data['twitter']; ?>/followers"><?php echo number_format($data['ValFollowers']); ?></a></td>
				<td><a target="_blank" href="http://www.youtube.com/user/<?php echo $data['youtube']; ?>"><?php echo number_format($data['ValYoutube']); ?></a></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7">Last update: <?php echo $data['ValLastUpdate'];?></td>
			</tr>
		</tfoot>
	</table>
		
	<div>
		<form method="POST">
			<p><input name="wp_seo_rank_feedburner" type="text" value="<?php echo $data['feedburner']; ?>" /> FeedBurner</p>
			<p><input name="wp_seo_rank_twitter" type="text" value="<?php echo $data['twitter']; ?>" /> Twitter</p>
			<p><input name="wp_seo_rank_youtube" type="text" value="<?php echo $data['youtube']; ?>" /> Youtube</p>
			<?php if($data['autoupdate'] == 'on') $checkedSeo = ' checked="checked"';?>
			<input name="wp_seo_rank_autoupdate" type="checkbox"<?php echo $checkedSeo;?> /> Auto Update Every Day</p>
			<p><input name="wp_seo_rank_save" type="submit" class="button" value="Save"> <input type="submit" name="wp_seo_rank_update" class="button" value="Force Update Now!"></p>
		</form>
	</div>
	<?
}

function update_seoA(){
	$data = get_option('wp_seo_rank');
	if($data['autoupdate'] == 'on')
		update_seo();
}
function update_seo($force=null){
	$data = get_option('wp_seo_rank');
	$data['feedburner'] = $data['feedburner'];
    $data['twitter'] = $data['twitter'];
    $data['youtube'] = $data['youtube'];
    $data['autoupdate'] = $data['autoupdate'];

	$url=site_url();
	$fecha=explode(' ',$data['ValLastUpdate']);
	$hoy=date('Y-m-d');
	if($force==1){
		$hoy="1900-01-01";
	}
	
	if($hoy!=$fecha[0]){
		
		$data['ValPageRank'] = getprSeo($url);
		$data['ValAlexaRank'] = alexaRank($url);
		$data['ValBacklinksGoogle'] = get_backlinks_google($url);
		$data['ValBacklinksYahoo'] = getYahooLinks($url);
		$data['ValFeedBurner'] = getFeedBurner($data["feedburner"]);
		$data['ValFollowers'] = followerSeo($data["twitter"]);
		$data['ValYoutube'] = GetSubscriberCountYoutube($data["youtube"]);
		$data['ValLastUpdate'] = date('Y-m-d');
		update_option('wp_seo_rank', $data);
	}

}

/*************************
 * FUNCIONES SEO
 *************************/
 

 
function followerSeo($user){

	$xml=simplexml_load_file("http://twitter.com/users/show.xml?screen_name=$user");
	$resp=$xml->followers_count;
	return trim($resp);

}
function GetSubscriberCountYoutube($user){
	$xmlData = file_get_contents('http://gdata.youtube.com/feeds/api/users/' . strtolower($user));  
	$xmlData = str_replace('yt:', 'yt', $xmlData); 
	$xml = new SimpleXMLElement($xmlData);   
	$resp=$xml->ytstatistics['subscriberCount'];
	return trim($resp);
}

function alexaRank($domain){
    $remote_url = 'http://data.alexa.com/data?cli=10&dat=snbamz&url='.trim($domain);
    $search_for = '<POPULARITY URL';
    if ($handle = @fopen($remote_url, "r")) {
        while (!feof($handle)) {
            $part .= fread($handle, 100);
            $pos = strpos($part, $search_for);
            if ($pos === false)
            continue;
            else
            break;
        }
        $part .= fread($handle, 100);
        fclose($handle);
    }
    $str = explode($search_for, $part);
    $str = array_shift(explode('"/>', $str[1]));
    $str = explode('TEXT="', $str);
 
    return $str[1];
}
function get_backlinks_google($url){
	$content = file_get_contents('http://ajax.googleapis.com/ajax/services/search/web?v=1.0&filter=0&key=ABQIAAAA6f5Achoodo5s2Q2049vn6BSIkO30j4gnxwlOBxQkFXOonq3PsBQ0hUYBhAxwx8DYL03zbFQWDSv_nA&q=link:' . urlencode($url));		
	$data = json_decode($content);
	return intval($data->responseData->cursor->estimatedResultCount);	
 
}
function getYahooLinks($dominio) {
	$appid = "31245124213";
	$feed="http://search.yahooapis.com/SiteExplorerService/V1/inlinkData?appid=".$appid."&query=$dominio&entire_site=1&omit_inlinks=domain";
	$contenido = @file_get_contents($feed);
	preg_match('/totalResultsAvailable=("(.*)"?)/', $contenido, $treffer);
	$total=str_replace('"','',$treffer[1]);
	return $total;			
 
}

function getFeedBurner($user){

	$xml=file_get_contents("https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=http://feeds.feedburner.com/$user");
	
	return get_match('/circulation="(.*)"/isU',$xml);

}
function get_match($regex,$content){
  preg_match($regex,$content,$matches);
  return $matches[1];
}

function getprSeo($url){
	$dc = "http://toolbarqueries.google.com";
	$gpr = new GooglePageRank(trim($url));
	$pagerank = $gpr->getPageRank($dc);
	return $pagerank;
	
}

add_action("wp_login", 'update_seoA');



/******************************
 * CLASS PAGERANK
 * ****************************/
 
 
class GooglePageRank {

	var $_GOOGLE_MAGIC = 0xE6359A60;
	var $_url = '';
	var $_checksum = '';

	function GooglePageRank($url)
	{
		$this->_url = $url;
	}

	function _strToNum($Str, $Check, $Magic)
	{
		$Int32Unit = 4294967296;

		$length = strlen($Str);
		for ($i = 0; $i < $length; $i++) {
			$Check *= $Magic;    

			if ($Check >= $Int32Unit) {
				$Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
				$Check = ($Check < -2147483647) ? ($Check + $Int32Unit) : $Check;
			}
			$Check += ord($Str{$i});
		}
		return $Check;
	}

	function _hashURL($String)
	{
		$Check1 = $this->_strToNum($String, 0x1505, 0x21);
		$Check2 = $this->_strToNum($String, 0, 0x1003F);

		$Check1 >>= 2;
		$Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
		$Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
		$Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);   

		$T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
		$T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );

		return ($T1 | $T2);
	}

	function checksum()
	{
		if($this->_checksum != '') return $this->_checksum;

		$Hashnum = $this->_hashURL($this->_url);

		$CheckByte = 0;
		$Flag = 0;

		$HashStr = sprintf('%u', $Hashnum) ;
		$length = strlen($HashStr);

		for ($i = $length - 1;  $i >= 0;  $i --) {
			$Re = $HashStr{$i};
			if (1 == ($Flag % 2)) {
				$Re += $Re;
				$Re = (int)($Re / 10) + ($Re % 10);
			}
			$CheckByte += $Re;
			$Flag ++;
		}

		$CheckByte %= 10;
		if (0 !== $CheckByte) {
			$CheckByte = 10 - $CheckByte;
			if (1 === ($Flag%2) ) {
				if (1 === ($CheckByte % 2)) {
					$CheckByte += 9;
				}
				$CheckByte >>= 1;
			}
		}

		$this->_checksum = '7'.$CheckByte.$HashStr;
		return $this->_checksum;
	}

	function pageRankUrl($dcchosen)
	{
		return $dcchosen . '/search?client=navclient-auto&features=Rank:&q=info:'.$this->_url.'&ch='.$this->checksum();
	}

	function getPageRank($dcchosen)
	{
		$fh = @fopen($this->pageRankUrl($dcchosen), "r");
		if($fh)
		{
			$contenido = '';
			while (!feof($fh)) {
			  $contenido .= fread($fh, 8192);
			}
			fclose($fh);
			ltrim($contenido);
			rtrim($contenido);
			$contenido=str_replace("Rank_1:1:","",$contenido);
			$contenido=str_replace("Rank_1:2:","",$contenido);
			//$contenido=intval($contenido);
			$contenido=intval($contenido);
			
			if(is_numeric($contenido))
				return $contenido;
			else
				return -2;
		}
		return -1;
	}

}
?>
