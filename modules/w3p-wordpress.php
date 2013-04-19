<?php
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
