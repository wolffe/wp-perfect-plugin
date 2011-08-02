<?php
// default values
add_option('all_in_one_google_webmaster', '');
add_option('all_in_one_bing_webmaster', '');
add_option('all_in_one_yahoo_webmaster', '');
add_option('all_in_one_alexa_webmaster', '');
add_option('all_in_one_bcatalog_webmaster', '');
add_option('all_in_one_fbinsights_webmaster', '');

add_option('all_in_one_google_analytics', '');
add_option('sitemap_URL', '');
add_option('all_in_one_compete_analytics', '');
add_option('all_in_one_sitemeter_analytics', '');

add_option('all_in_one_head_section', '');
add_option('all_in_one_footer_section', '');

function all_in_one_webmaster_head() {
	$google_wm = get_option('all_in_one_google_webmaster');
	$alexa_wm = get_option('all_in_one_alexa_webmaster');
	$bcatalog_wm = get_option('all_in_one_bcatalog_webmaster');
	$fbinsights_wm = get_option('all_in_one_fbinsights_webmaster');

	$bing_wm = get_option('all_in_one_bing_webmaster');
	$yahoo_wm = get_option('all_in_one_yahoo_webmaster');
	$google_an = get_option('all_in_one_google_analytics');

	$head_section = get_option('all_in_one_head_section');

	if(!($head_section == "")) {
		echo $head_section . "\n";
	}
	if(!($google_wm == "")) {
		$google_wm_meta = '<meta name="google-site-verification" content="' . $google_wm . '" /> ';
		echo $google_wm_meta . "\n";
	}
	if(!($bing_wm == "")) {
		$bing_wm_meta = '<meta name="msvalidate.01" content="' . $bing_wm . '" />';
		echo $bing_wm_meta . "\n";
	}
	if(!($yahoo_wm == "")) {
		$yahoo_wm_meta = '<meta name="y_key" content="' . $yahoo_wm . '" />';
		echo $yahoo_wm_meta . "\n";
	}
	if(!($alexa_wm == "")) {
		$alexa_wm_meta = '<meta name="alexaVerifyID" content="' . $alexa_wm . '" />';
		echo $alexa_wm_meta . "\n";
	}
	if(!($bcatalog_wm == "")) {
		$bcatalog_wm_meta = '<meta name="blogcatalog" content="' . $bcatalog_wm . '" />';
		echo $bcatalog_wm_meta . "\n";
	}
	if(!($google_an == "")) {
		echo '<script type="text/javascript">'."\n";
		echo 'var _gaq = _gaq || [];'."\n";
		echo '_gaq.push([\'_setAccount\', \'' . $google_an . '\']);'."\n";
		echo '_gaq.push([\'_trackPageview\']);'."\n";
		echo '_gaq.push([\'_trackPageLoadTime\']);'."\n";
		echo '(function() {'."\n";
		echo 'var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;'."\n";
		echo 'ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';'."\n";
		echo 'var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);'."\n";
		echo ' })();'."\n";
		echo '</script>'."\n";
	}
}

function all_in_one_webmaster_footer() {
	$compete_an = get_option('all_in_one_compete_analytics');

	$footer_section = get_option('all_in_one_footer_section');
	$sitemeter_an = get_option('all_in_one_sitemeter_analytics');

	if(!($footer_section == "")) {
		echo $footer_section . "\n";
	}
	if(!($compete_an == "")) {
		echo '<script type="text/javascript">'."\n";
		echo '__compete_code = \'' . $compete_an . '\';'."\n";
		echo '(function () { var s = document.createElement(\'script\'),d = document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0],t = \'https:\' == document.location.protocol ? \'https://c.compete.com/bootstrap/\' : \'http://c.compete.com/bootstrap/\'; s.src = t + __compete_code + \'/bootstrap.js\'; s.type = \'text/javascript\'; s.async = \'async\'; if (d) { d.appendChild(s); }}());'."\n";
		echo '</script>'."\n";
	}
	if(!($sitemeter_an == "")) {
		echo '<script type="text/javascript" src="'.$sitemeter_an.'"></script>'."\n";
	}
}
function all_in_one_webmaster_options_page() {
	if(isset($_POST['info_update1'])) {
		update_option('all_in_one_google_webmaster', (string)$_POST["all_in_one_google_webmaster"]);
		update_option('all_in_one_alexa_webmaster', (string)$_POST["all_in_one_alexa_webmaster"]);
		update_option('all_in_one_bcatalog_webmaster', (string)$_POST["all_in_one_bcatalog_webmaster"]);
		update_option('all_in_one_bing_webmaster', (string)$_POST["all_in_one_bing_webmaster"]);
		update_option('all_in_one_yahoo_webmaster', (string)$_POST['all_in_one_yahoo_webmaster']);
		update_option('all_in_one_google_analytics', (string)$_POST['all_in_one_google_analytics']);
		update_option('all_in_one_compete_analytics', (string)$_POST['all_in_one_compete_analytics']);

		update_option('all_in_one_sitemeter_analytics', stripslashes_deep((string)$_POST['all_in_one_sitemeter_analytics']));
		update_option('all_in_one_head_section', stripslashes_deep((string)$_POST['all_in_one_head_section']));
		update_option('all_in_one_footer_section', stripslashes_deep((string)$_POST['all_in_one_footer_section']));

		echo '<div id="message" class="updated fade"><p><strong>Settings updated.</strong></p></div>';
	}
	?>
	<div class=wrap>
		<div id="icon-options-general" class="icon32"></div>
		<h2>Basic Settings</h2>
		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<input type="hidden" name="info_update1" id="info_update1" value="true" />
			<h3>Webmaster Options</h3>
			<p>
				<input name="all_in_one_google_webmaster" type="text" size="55" value="<?php echo get_option('all_in_one_google_webmaster'); ?>" /> <label>Google Webmaster Central</label>
				<br /><small class="description">(meta name="google-site-verification" content="<code>Volxdfasfasd3i3e_wATasfdsSDb0uFqvNVhLk7ZVY</code>")</small>
			</p>
			<p>
				<input name="all_in_one_bing_webmaster" type="text" size="55" value="<?php echo get_option('all_in_one_bing_webmaster'); ?>" /> <label>Bing Webmaster Center</label>
				<br /><small class="description">(meta name="msvalidate.01" content="<code>ASBKDW71D43Z67AB2D39636C89B88A</code>")</small>
			</p>
			<p>
				<input name="all_in_one_yahoo_webmaster" type="text" size="55" value="<?php echo get_option('all_in_one_yahoo_webmaster'); ?>" /> <label>Yahoo Site Explorer</label>
				<br /><small class="description">(meta name="y_key" content="<code>98D5S31a48dd7fc</code>")</small>
			</p>
			<p>
				<input name="all_in_one_alexa_webmaster" type="text" size="55" value="<?php echo get_option('all_in_one_alexa_webmaster'); ?>" /> <label>Alexa Rank</label>
				<br /><small class="description">(meta name="alexaVerifyID" content="<code>OKJ3RsasdfKHGST1uqa8zcBfrjtY</code>")</small>
			</p>
			<p>
				<input name="all_in_one_bcatalog_webmaster" type="text" size="55" value="<?php echo get_option('all_in_one_bcatalog_webmaster'); ?>" /> <label>Blog Catalog</label>
				<br /><small class="description">(meta name="blogcatalog" content="<code>7DS9234212</code>")</small>
			</p>

			<hr />
			<h3>Analytics Options</h3>
			<p>
				<input name="all_in_one_google_analytics" type="text" size="55" value="<?php echo get_option('all_in_one_google_analytics'); ?>" /> <label>Google Analytics</label>
				<br /><small class="description">(Web Property ID: <code>UA-XXXXXXX-X</code>)</small>
			</p>
			<p>
				<input name="all_in_one_compete_analytics" type="text" size="55" value="<?php echo get_option('all_in_one_compete_analytics'); ?>" /> <label>Compete Analytics</label>
				<br /><small class="description">(__compete_code = '<code>07a543238f9kdwjga0d280bd70534990a</code>')</small>
			</p>
			<p>
				<input name="all_in_one_sitemeter_analytics" type="text" size="55" value="<?php echo get_option('all_in_one_sitemeter_analytics'); ?>" /> <label>SiteMeter Analytics/Tracking</label>
				<br /><small class="description">(src="<code>http://s44.sitemeter.com/js/counter.js?site=s44AShah</code>")</small>
			</p>

			<hr />
			<h3>Extra HTML code to be inserted in to Header or Footer Section</h3>
			<p>
				Header section: Add ONLY HTML code to the <code>head</code> of your blog<br />
				<textarea name="all_in_one_head_section" cols="60" rows="3"><?php echo get_option('all_in_one_head_section'); ?></textarea>
			</p>
			<p>
				Footer section: Add ONLY HTML code to the <code>footer</code> of your blog<br />
				<textarea name="all_in_one_footer_section" cols="60" rows="3"><?php echo get_option('all_in_one_footer_section'); ?></textarea>
			</p>

			<p><input type="submit" name="info_update1" class="button-primary" value="<?php _e('Update options'); ?>" /></p>
		</form>
	</div>
<?php
}
add_action('wp_head', 'all_in_one_webmaster_head');
add_action('wp_footer', 'all_in_one_webmaster_footer');
?>