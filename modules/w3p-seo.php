<?php
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

	<div>
		<form method="POST">
			<p>
				FeedBurner username: <input name="wp_seo_rank_feedburner" type="text" value="<?php echo $data['feedburner']; ?>" /> 
				<input name="wp_seo_rank_save" type="submit" class="button" value="Save Settings" />
			</p>
		</form>
	</div>

	<table class="widefat">
		<thead>
			<tr>
				<th>Alexa Rank</th>
				<th>Google PageRank</th>
				<th>Google Backlinks</th>
				<th>Yahoo Backlinks</th>
				<th>Feedburner Subscribers</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><a target="_blank" href="http://www.alexa.com/siteinfo/<?php echo site_url();?>"><?php echo number_format(alexaRank($w3p_tracked_site));?></a></td>
				<td><strong><?php echo getprSeo($w3p_tracked_site);?></strong></td>
				<td><a target="_blank" href="http://www.google.com/search?oe=utf8&ie=utf8&source=uds&start=0&filter=0&hl=en&q=link:<?php echo site_url(); ?>"><?php echo number_format(get_backlinks_google(site_url())); ?></a></td>
				<td><a target="_blank" href="http://siteexplorer.search.yahoo.com/search;_ylt=A0oG7zbdV5ZMIt8AdFddhMkF?p=<?php echo site_url(); ?>&y=Explore+URL&fr=sfp"><?php echo number_format(getYahooLinks(site_url())); ?></a></td>
				<td><a target="_blank" href="http://feeds.feedburner.com/<?php echo $data['feedburner']; ?>"><?php echo number_format(getFeedBurner($data['feedburner'])); ?></a></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7"><small>Last update: <?php echo $data['ValLastUpdate'];?></small></td>
			</tr>
		</tfoot>
	</table>
		
	<?
}

/*************************
 * SEO FUNCTIONS/ALGORYTHMS
 *************************/
function alexaRank($domain) {
	$remote_url = 'http://data.alexa.com/data?cli=10&dat=snbamz&url='.trim($domain);
	$search_for = '<POPULARITY URL';
	if($handle = @fopen($remote_url, "r")) {
		while(!feof($handle)) {
			$part .= fread($handle, 100);
			$pos = strpos($part, $search_for);
			if($pos === false)
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
function get_backlinks_google($url) {
	$content = file_get_contents('http://ajax.googleapis.com/ajax/services/search/web?v=1.0&filter=0&key=ABQIAAAA6f5Achoodo5s2Q2049vn6BSIkO30j4gnxwlOBxQkFXOonq3PsBQ0hUYBhAxwx8DYL03zbFQWDSv_nA&q=link:'.urlencode($url));
	$data = json_decode($content);
	return intval($data->responseData->cursor->estimatedResultCount);
}
function getYahooLinks($dominio) {
	$appid = "31245124213";
	$feed = 'http://search.yahooapis.com/SiteExplorerService/V1/inlinkData?appid='.$appid."&query=$dominio&entire_site=1&omit_inlinks=domain";
	$contenido = @file_get_contents($feed);
	preg_match('/totalResultsAvailable=("(.*)"?)/', $contenido, $treffer);
	$total = str_replace('"','',$treffer[1]);
	return $total;
}
function getFeedBurner($user) {
	$xml = file_get_contents("https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=http://feeds.feedburner.com/$user");
	return get_match('/circulation="(.*)"/isU',$xml);
}
function get_match($regex,$content) {
	preg_match($regex,$content,$matches);
	return $matches[1];
}
function getprSeo($url){
	$dc = 'http://toolbarqueries.google.com';
	$gpr = new GooglePageRank(trim($url));
	$pagerank = $gpr->getPageRank($dc);
	return $pagerank;
}

add_action('wp_login', 'update_seo');

/******************************
 * PAGERANK CLASS
 * ****************************/
class GooglePageRank {
	var $_GOOGLE_MAGIC = 0xE6359A60;
	var $_url = '';
	var $_checksum = '';

	function GooglePageRank($url) {
		$this->_url = $url;
	}
	function _strToNum($Str, $Check, $Magic) {
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

	function _hashURL($String) {
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

	function checksum() {
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
		if(0 !== $CheckByte) {
			$CheckByte = 10 - $CheckByte;
			if(1 === ($Flag%2) ) {
				if(1 === ($CheckByte % 2)) {
					$CheckByte += 9;
				}
				$CheckByte >>= 1;
			}
		}

		$this->_checksum = '7'.$CheckByte.$HashStr;
		return $this->_checksum;
	}
	function pageRankUrl($dcchosen) {
		return $dcchosen.'/tbr?client=navclient-auto&features=Rank:&q=info:'.$this->_url.'&ch='.$this->checksum();
	}
	function getPageRank($dcchosen) {
		$fh = @fopen($this->pageRankUrl($dcchosen), "r");
		if($fh) {
			$contenido = '';
			while(!feof($fh)) {
			  $contenido .= fread($fh, 8192);
			}
			fclose($fh);
			ltrim($contenido);
			rtrim($contenido);
			$contenido=str_replace("Rank_1:1:","",$contenido);
			$contenido=str_replace("Rank_1:2:","",$contenido);
			$contenido=intval($contenido);
			
			if(is_numeric($contenido))
				return $contenido;
			else
				return -2;
		}
		return -1;
	}
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