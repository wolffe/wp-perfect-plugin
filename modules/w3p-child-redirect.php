<?php
/*
Plugin Name: Redirect Parent to First Child
Plugin URI: http://www.nathanrice.net/parent-to-first-child
Description: This plugin will do a 301 redirect on top-level parent pages, to their first child page, based first on menu order, then post title if no menu order is set.  It only redirects if a *published* child page actually exists.
Version: 0.1
Author: Nathan Rice
Author URI: http://www.nathanrice.net/

License: This plugin is licensed under GPL.  
*/

//This function checks to see if the page has children
//Returns true if the page has children, returns false if it doesn't.
function has_children() {
	//globalize the query var, and locate the page ID
	global $wp_query; $page_id = $wp_query->queried_object->ID;
	
	if(get_pages('child_of='.$page_id)) return TRUE;
	else return FALSE;
}

//This function checks to see if the page has published children
//Returns true if the page has published children, returns false if it doesn't.
function has_published_children() {
	//globalize the query var, and locate the page ID
	global $wp_query; $page_id = $wp_query->queried_object->ID;
	
	$children = get_pages('child_of='.$page_id.'&sort_column=menu_order,post_title');
	foreach((array)$children as $child) {
		if($child->post_status == 'publish') return TRUE; break;
	}
}

//This function checks to see if the page is top-level (has_parent)
//Returns true if the page has a parent, returns false if it doesn't.
function has_parent() {
	//globalize the query var, and locate any parent pages
	global $wp_query; $page_parent = $wp_query->queried_object->post_parent;
	
	if($page_parent) return TRUE;
	else return FALSE;
}

//This function returns the ID of the first published child page
function get_first_published_child_id() {
	//globalize the query var, and locate the page ID
	global $wp_query; $page_id = $wp_query->queried_object->ID;
	
	$children = get_pages('child_of='.$page_id.'&sort_column=menu_order,post_title');
	foreach((array)$children as $child) {
		if($child->post_status == 'publish') return $child->ID; break;
	}
}

//Hook the function into the template_redirect action
add_action('template_redirect','redirect_to_first_child');
function redirect_to_first_child() {
	//globalize the query var, and locate the page ID
	global $wp_query; $page_id = $wp_query->queried_object->ID;
	
	//If all these conditions are met...
	//Is a page ... has children ... has published children ... does not have a parent...
	if(is_page() && has_children() && has_published_children && !has_parent()) {
		//Get the children (that sounds weird) ...
		$children = get_pages('child_of='.$page_id.'&sort_column=menu_order,post_title');
		//Get the permalink for the first child ...
		$redirect = get_permalink(get_first_published_child_id());
		//And do the redirect.
		Header( "HTTP/1.1 301 Moved Permanently" ); 
		Header( "Location: $redirect" );
	}
}
?>