<?php
function w3p_dashboard_output() {
	global $w3p_widget_title;
	$widget_options = w3p_dashboard_options();
	$w3p_feed_source_url = apply_filters('w3p_filter_feed_source_url', 'http://getbutterfly.com/feed/');

	echo '<div id="gbnews-rss-widget" class="rss-widget">';
		wp_widget_rss_output(array(
			'url'          => esc_url($w3p_feed_source_url),
			'title'        => esc_attr__($w3p_widget_title),
			'meta'         => array('target' => '_blank'),
			'items'        => $widget_options['posts_number'],
			'show_summary' => 0,
			'show_author'  => 0,
			'show_date'    => 1
		));
	echo '</div>';
}
function w3p_add_dashboard_widgets() {
	global $w3p_filter_capability, $w3p_widget_title;
	$w3p_widget_title = apply_filters('w3p_filter_widget_title', __('getButterfly.com Planet', 'w3p'));
	$w3p_filter_capability = apply_filters('w3p_filter_capability_all', 'read');

	if(current_user_can($w3p_filter_capability)) {
		wp_add_dashboard_widget('gbnews_dashboard_feed', esc_attr__( $w3p_widget_title ), 'w3p_dashboard_output', 'w3p_dashboard_setup');
	}
}
function w3p_dashboard_options() {
	$defaults = array('posts_number' => 5);
	if((!$options = get_option('gbnews_dashboard_feed')) || !is_array($options)) {
		$options = array();
	}

	return array_merge($defaults, $options);
}
function w3p_dashboard_setup() {
	$options = w3p_dashboard_options();
	if('post' == strtolower($_SERVER['REQUEST_METHOD']) && isset($_POST['widget_id']) && 'gbnews_dashboard_feed' == $_POST['widget_id']) {
		foreach(array('posts_number') as $key) {
			$options[$key] = $_POST[$key];
		}
		update_option('gbnews_dashboard_feed', $options);
	}

	echo '<p><label for="posts_number">' . __( 'How many items would you like to display?', 'w3p' ) .
		'<select id="posts_number" name="posts_number">';
			for($i=3; $i<=20; $i=$i+1) {
				echo '<option value="' . $i . '"' . ($options['posts_number'] == $i ? ' selected' : '""') . '>' . $i . '</option>';
			}
	echo '</select></label></p>';
}
?>
