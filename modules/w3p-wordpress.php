<?php
// Perfect Login Screen
function w3p_login_logo() {
	echo '
	<style type="text/css">
	h1 a { background-image:url('.W3P_PLUGIN_URL.'/images/logo-login.png) !important; }
	.message-custom { color: #666666; text-align: center; }
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
	$message = '<p class="message-custom"><small>Welcome!</small></p>';
	return $message;
}

add_action('login_head', 'w3p_login_logo');
add_filter('login_headerurl', 'w3p_login_url');
add_filter('login_headertitle', 'w3p_login_title');
//add_filter('login_message', 'w3p_login_message');
?>
