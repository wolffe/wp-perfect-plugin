<?php
/*
Plugin Name: List Subpages
Plugin URI: http://robm.me.uk/projects/plugins/wordpress/list-subpages/
Description: Adds a post tag that lists the sub pages of the current page, allowing you to use parent pages in a similar way to categories.
Author: Rob Miller
Version: 2.1
Author URI: http://pwnt.co.uk/blog/
*/

/**
 * Generates and optionally outputs the hierarchy of pages corresponding to the current one.
 * @param string $query
 * @return string The generated hierarchy.
 */
function list_subpages( $query = '' ) {
	global $wpdb, $post;
	
	parse_str($query, $q);
	
	if ( empty($q['child_of']) && empty($post->ID) )
		return;
	
	$defaults = array(
		'child_of' => $post->ID,
		'echo' => 1,
		'title_li' => '',
		'depth' => 0,
		'include' => '',
		'exclude' => ''
	);
	$options = array_merge($defaults, $q);
	
	// See if there are any subpages of this page
	$subpages = $wpdb->get_var("
	SELECT COUNT(*) FROM $wpdb->posts
	WHERE post_parent = '{$options['child_of']}' AND post_status = 'publish' AND post_type = 'page'
	");
	
	if ( $subpages <= 0 )
		return;
	
	$query = 'echo=0&';
	foreach ( (array) $options as $key => $value ) {
		if ( $key != 'echo' )
			$query .= "$key=$value&";
	}
	
	$html = "
		<ul>
	";
	
	$html .= wp_list_pages($query);
	
	$html .= '
		</ul>
	';
	
	if ( $options['echo'] )
		echo $html;
	return $html;
}

/**
 * Filters post content and, if the post is static and contains the list subpages tag, replaces the tag with the generated hierarchy.
 * @param string $content The content of the post that we're filtering.
 * @return string The newly-filtered post content.
 */
function parse_list_subpages( $content ) {
	global $wpdb, $post;
	
	// The way static pages are handled changed between 2.0 and 2.1.
	$static = ( $post->post_status == 'static' || $post->post_type == 'page' );
	// Only parse pages, not regular posts.
	if ( !$static )
		return $content;
	
	$attributes = array('full', 'depth', 'order', 'class', 'exclude', 'show_date', 
	'date_format', 'child_of', 'title_li', 'authors');
	
	// Parse out our tags.
	preg_match_all('#<subpages([^>]*)/>#si', $content, $tags, PREG_SET_ORDER);
	
	foreach ( $tags as $tag ) {
		foreach ( (array) $attributes as $attr ) {
			preg_match('#' . preg_quote($attr) . '="([^"]+)"#i', $tag[0], $m);
			if ( !empty($m) )
				$$attr = $m[1][0];
		}
		
		$query = 'echo=0';
		if ( empty($child_of) )
			$query .= "&child_of=$post->ID";
		if ( empty($title_li) )
			$query .= "&title_li=Subpages of %E2%80%9C{$post->post_title}%E2%80%9D:"; // Ugh, URL encoding.
		
		foreach ( (array) $attributes as $attr ) {
			if ( !empty($$attr) ) {
				$value = $$attr;
				$query .= "&$attr=$value";
			}
		}
		
		$subpages = list_subpages($query);
		$content = str_replace($tag[0], $subpages, $content);
	}
	
	return $content;
}
add_filter('the_content', 'parse_list_subpages');


/**
 * Adds a button to the posting toolbar for easy insertion of the list subpages tag.
 */
function list_subpages_button() {
	if ( strpos($_SERVER['REQUEST_URI'], 'page-new.php') !== false ||
		 strpos($_SERVER['REQUEST_URI'], 'post.php?action=edit') !== false ) {
		echo '
			<script language="javascript" type="text/javascript"><!--
			var toolbar = document.getElementById("ed_toolbar");
		';
		edit_insert_button('subpages', 'list_subpages_button', 'Creates a list of subpages at this point');
		echo '
			function list_subpages_button() {
				edInsertContent(edCanvas, "<!-"+"-subpages-"+"->");
			}
			//--></script>
		';
	}
}

if ( !function_exists('edit_insert_button') ) {
	
	function edit_insert_button( $caption, $js_onclick, $title = '' ) {
		echo "
			if (toolbar)
			{
				var theButton = document.createElement('input');
				theButton.type = 'button';
				theButton.value = '$caption';
				theButton.onclick = $js_onclick;
				theButton.className = 'ed_button';
				theButton.title = '$title';
				theButton.id = 'ed_$caption';
				toolbar.appendChild(theButton);
			}
		";
	}
}
add_filter('admin_footer', 'list_subpages_button');

?>
