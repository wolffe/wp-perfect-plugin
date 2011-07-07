<?php
// Perfect Login Screen
function w3p_login_logo() {
	echo '
	<style type="text/css">
	h1 a { background-image:url('.W3P_PLUGIN_URL.'/images/logo-login.png) !important; }
	</style>
	';
}
function w3p_login_url() {
	echo bloginfo('url');
	//return 'http://www.blogtycoon.net/'; // Replace with custom URL
}
function w3p_login_title() {
	echo get_option('blogname');
}
function w3p_login_message() {
	$message = '<p class="message"><small>Welcome!</small></p>';
	return $message;
}

add_action('login_head', 'w3p_login_logo');
add_filter('login_headerurl', 'w3p_login_url');
add_filter('login_headertitle', 'w3p_login_title');
//add_filter('login_message', 'w3p_login_message');

// Perfect Dashboard Branding
function w3p_admin_footer_text( $default_text ) {
	return '<span id="footer-thankyou">Website managed by <a href="http://www.blogtycoon.net/">Blog Tycoon</a><span> | Powered by <a href="http://wordpress.org/">WordPress</a>';
}

// Perfect Custom RSS Feed
function w3p_dashboard_widgets() {
	global $wp_meta_boxes;
	// remove unnecessary widgets
	// var_dump( $wp_meta_boxes['dashboard'] ); // use to get all the widget IDs
	unset(
		$wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'],
		$wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'],
		$wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']
	);
	// add a custom dashboard widget
	wp_add_dashboard_widget('dashboard_custom_feed', 'News from Blog Tycoon', 'dashboard_custom_feed_output'); //add new RSS feed output
}
function dashboard_custom_feed_output() {
	echo '<div class="rss-widget">';
	wp_widget_rss_output(array(
		'url' => 'http://www.blogtycoon.net/feed',
		'title' => 'News from Blog Tycoon',
		'items' => 2,
		'show_summary' => 1,
		'show_author' => 0,
		'show_date' => 1 
	));
	echo '</div>';
}

add_action('wp_dashboard_setup', 'w3p_dashboard_widgets');
add_filter('admin_footer_text', 'w3p_admin_footer_text');

// Remove unnecessary links from the menu
function w3p_admin_menu() {
	remove_menu_page('link-manager.php');
}
add_action('admin_menu', 'w3p_admin_menu');

// Add warnings
function w3p_admin_notice() {
	global $current_screen;
	if($current_screen->parent_base == 'options-general')
		echo '<p class="message-box alert">Warning - changing settings on these pages may cause problems with your website\'s design!</p>';
}
add_action('admin_notices', 'w3p_admin_notice');

// Various // To be sorted
// This module helps you to keep the link juice by redirecting all non-existing URLs which normally return a 404 error to the front blog page using 301 redirect.
class noMissingPages {
	// Constructor
	function noMissingPages() {
		add_filter('status_header', array(&$this, 'status_header'), 100, 2);
	}
	function status_header($status_header, $header) {
		if($header == 404) {
			// Extract root dir from blog url
			$root = '/';
			if(preg_match('#^http://[^/]+(/.+)$#', get_option('siteurl'), $matches)) {
				$root = $matches[1];
			}
			// Make sure it ends with slash
			if($root[strlen($root) - 1] != '/') {
				$root .= '/';
			}
			// Check if request is not for GWT verification file
			if(strpos( $_SERVER['REQUEST_URI'], $root.'noexist_' ) !== 0) {
				wp_redirect(get_bloginfo('siteurl'), 301);
				exit();
			}
		}
		return $status_header;
	}
}
$wp_no_missing_pages = new noMissingPages();
?>
