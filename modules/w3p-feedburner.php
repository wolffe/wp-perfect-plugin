<?php
$data = array(
	'feedburner_url' => '',
	'feedburner_comments_url' => ''
);

$ol_flash = '';

add_option('feedburner_settings', $data, '', 'yes');

$feedburner_settings = get_option('feedburner_settings');

function fb_is_hash_valid($form_hash) {
	$ret = false;
	$saved_hash = fb_retrieve_hash();
	if ($form_hash === $saved_hash) {
		$ret = true;
	}
	return $ret;
}

function fb_generate_hash() {
	return md5(uniqid(rand(), TRUE));
}

function fb_store_hash($generated_hash) {
	return update_option('feedsmith_token',$generated_hash,'FeedSmith Security Hash');
}

function fb_retrieve_hash() {
	$ret = get_option('feedsmith_token');
	return $ret;
}

function ol_feedburner_options_subpanel() {
	global $ol_flash, $feedburner_settings, $_POST, $wp_rewrite;

	// Easiest test to see if we have been submitted to
	if(isset($_POST['feedburner_url']) || isset($_POST['feedburner_comments_url'])) {
		// Now we check the hash, to make sure we are not getting CSRF
		if(fb_is_hash_valid($_POST['token'])) {
			if (isset($_POST['feedburner_url'])) { 
				$feedburner_settings['feedburner_url'] = $_POST['feedburner_url'];
				update_option('feedburner_settings',$feedburner_settings);
				$ol_flash = "Your settings have been saved.";
			}
			if (isset($_POST['feedburner_comments_url'])) { 
				$feedburner_settings['feedburner_comments_url'] = $_POST['feedburner_comments_url'];
				update_option('feedburner_settings',$feedburner_settings);
				$ol_flash = "Your settings have been saved.";
			} 
		} else {
			// Invalid form hash, possible CSRF attempt
			$ol_flash = "Security hash missing.";
		} // endif fb_is_hash_valid
	} // endif isset(feedburner_url)
	
	if ($ol_flash != '') echo '<div id="message" class="updated fade"><p>' . $ol_flash . '</p></div>';
	
	$temp_hash = fb_generate_hash();
	fb_store_hash($temp_hash);
	echo '<div class="wrap">';
	echo '<div id="icon-options-general" class="icon32"></div>';
	echo '<h2>Google FeedBurner Settings</h2>';
	echo '
		<p>This module redirects traffic for your feeds to a Google FeedBurner feed you have created. Google FeedBurner can then track all of your feed subscriber traffic and usage and apply a variety of features you choose to improve and enhance your original WordPress feed. Google FeedBurner\'s services allow publishers who already have a feed to improve their understanding of and relationship with their audience. Once you have a working feed, run it through FeedBurner and realize a whole new set of benefits.</p>
		<form action="" method="post">
			<input type="hidden" name="redirect" value="true" />
			<input type="hidden" name="token" value="'.fb_retrieve_hash().'" />
			<ol>
				<li>To get started, <a href="http://feedburner.google.com/fb/a/home" target="_blank">create a FeedBurner feed for '.get_bloginfo('name').'</a>. This feed will handle all traffic for your posts.</li>
				<li>Once you have created your FeedBurner feed, enter its address into the field below (<code>http://feeds.feedburner.com/yourfeedhere</code>):<br /><input type="text" name="feedburner_url" value="'.htmlentities($feedburner_settings['feedburner_url']).'" size="45" /></li>
				<li>Optional: If you also want to handle your WordPress comments feed using FeedBurner, <a href="http://feedburner.google.com/fb/a/home" target="_blank">create a FeedBurner comments feed</a> and then enter its address below:<br /><input type="text" name="feedburner_comments_url" value="'.htmlentities($feedburner_settings['feedburner_comments_url']).'" size="45" />
			</ol>
			<p><input type="submit" value="Save" class="button-primary" /></p>
		</form>';
	echo '</div>';
}

function ol_feed_redirect() {
	global $wp, $feedburner_settings, $feed, $withcomments;
	if (is_feed() && $feed != 'comments-rss2' && !is_single() && $wp->query_vars['category_name'] == '' && ($withcomments != 1) && trim($feedburner_settings['feedburner_url']) != '') {
		if (function_exists('status_header')) status_header( 302 );
		header("Location:" . trim($feedburner_settings['feedburner_url']));
		header("HTTP/1.1 302 Temporary Redirect");
		exit();
	} elseif (is_feed() && ($feed == 'comments-rss2' || $withcomments == 1) && trim($feedburner_settings['feedburner_comments_url']) != '') {
		if (function_exists('status_header')) status_header( 302 );
		header("Location:" . trim($feedburner_settings['feedburner_comments_url']));
		header("HTTP/1.1 302 Temporary Redirect");
		exit();
	}
}

function ol_check_url() {
	global $feedburner_settings;
	switch (basename($_SERVER['PHP_SELF'])) {
		case 'wp-rss.php':
		case 'wp-rss2.php':
		case 'wp-atom.php':
		case 'wp-rdf.php':
			if (trim($feedburner_settings['feedburner_url']) != '') {
				if (function_exists('status_header')) status_header( 302 );
				header("Location:" . trim($feedburner_settings['feedburner_url']));
				header("HTTP/1.1 302 Temporary Redirect");
				exit();
			}
			break;
		case 'wp-commentsrss2.php':
			if (trim($feedburner_settings['feedburner_comments_url']) != '') {
				if (function_exists('status_header')) status_header( 302 );
				header("Location:" . trim($feedburner_settings['feedburner_comments_url']));
				header("HTTP/1.1 302 Temporary Redirect");
				exit();
			}
			break;
	}
}

if (!preg_match("/feedburner|feedvalidator/i", $_SERVER['HTTP_USER_AGENT'])) {
	add_action('template_redirect', 'ol_feed_redirect');
	add_action('init','ol_check_url');
}
?>
