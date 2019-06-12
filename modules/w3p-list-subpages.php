<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function w3p_subpages() {
	global $wpdb, $post;

	$defaults = [
		'child_of' => $post->ID,
		'echo' => 1,
		'title_li' => '',
		'depth' => 0,
		'include' => '',
		'exclude' => ''
	];

	$subpages = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent = '{$defaults['child_of']}' AND post_status = 'publish' AND post_type = 'page';");

	if ((int) $subpages <= 0) {
		return;
	}

	$query = 'echo=0&';

	foreach ((array) $defaults as $key => $value) {
		if ($key != 'echo') {
			$query .= $key . '=' . $value . '&';
		}
	}

	$html = '<ul class="w3p-subpages">';
		$html .= wp_list_pages($query);
	$html .= '</ul>';

	return $html;
}

add_shortcode('subpages', 'w3p_subpages');
