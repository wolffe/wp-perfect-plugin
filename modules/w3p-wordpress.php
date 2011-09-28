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
	return '<span id="footer-thankyou">Website managed by <a href="http://getbutterfly.com/">getButterfly</a><span> | Powered by <a href="http://wordpress.org/">WordPress</a>';
}

add_filter('admin_footer_text', 'w3p_admin_footer_text');

// Remove unnecessary links from the menu
function w3p_admin_menu() {
	remove_menu_page('link-manager.php');
}
add_action('admin_menu', 'w3p_admin_menu');

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
