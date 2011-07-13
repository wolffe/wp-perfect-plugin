<?php 
/*
Plugin Name: Simple Google Street View
Description: Adds the functionality to add Google Street View windows to your posts and pages without the need of a google API key.
Version: 0.4
Author: PJ Volders
Author URI: http://www.pjvolders.be/
License: GPL2
*/

/*  Copyright 2010  Pieter-Jan Volders  (email : support@pjvolders.be)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*	THEME */

// Run the filter when a blog is shown
add_filter( 'the_content', 'filter_simple_streetview' );

function filter_simple_streetview($content) {
	//print($content);
	preg_match_all("/\[streetview([^\]]*)\](.*?)\[\/streetview\]/", $content, $matches);
	
	foreach ($matches[0] as $k=>$match)
	{
		$attributes = $matches[1][$k];
		$string = $matches[2][$k];
		$content = str_replace($match, simple_streetview_div($string, $attributes, $k), $content);
	}

	return $content;
}

function simple_streetview_div($string, $attr_string, $k=0) {

	if ($attributes = simple_streetview_attr2arr($attr_string)) {
		
		$javascript = "<script type='text/javascript'>
		  	var myLatlng = new google.maps.LatLng(".$attributes['lat'].",".$attributes['lng'].");
			var panoramaOptions = {
			  position: myLatlng,
			  addressControl: false,
			  pov: {
				heading: ".$attributes['heading'].",
				pitch: ".$attributes['pitch'].",
				zoom: ".$attributes['zoom']."
			  }
			};
			var panorama_$k = new  google.maps.StreetViewPanorama(document.getElementById('streetview_canvas_$k'), panoramaOptions);
		</script>";
		unset($attributes['lat']);
		unset($attributes['lng']);
		unset($attributes['heading']);
		unset($attributes['pitch']);
		unset($attributes['zoom']);
		$style = simple_streetview_style($attributes);
		
	}
	$div = "<div id='streetview_canvas_$k' style='$style'></div>";
	
	return $div.$javascript;
}

function simple_streetview_attr2arr($attr) {
	// match the attributes
	if (preg_match_all('/(\S*)="([^"]*)"/', $attr, $matches)) {
		$attributes = $matches[1];
		$values		= $matches[2];
		// return the attributes in a attr=>value array
		return array_combine($attributes, $values);
	} else {
		return false;
	}
}

function simple_streetview_style($arr) {
	$style = '';
	foreach ($arr as $key => $value)
	{
		$style.= $key.': '.$value.'; ';
	}
	return $style;
	
}

// javascript for google maps
add_action( 'init', 'simple_streetview_gmjs' );

function simple_streetview_gmjs() {
	wp_register_script('googlemaps', 'http://maps.google.com/maps/api/js?sensor=false');
	wp_enqueue_script('googlemaps');
}


/* Editor */

// Adding media buttuns
add_action('media_buttons', 'addMediaButton', 99);

// Adding action for the iframe
add_action('media_upload_simple_streetview', 'streetview_create_iframe');


function addMediaButton() {

	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";

	$simple_streetview_upload_iframe_src = apply_filters('media_simple_streetview_iframe_src', "$media_upload_iframe_src&amp;type=simple_streetview");
	
	$simple_streetview_title = 'Add Google Street View';
	
	$logo = get_option('siteurl') . "/wp-content/plugins/" . dirname(plugin_basename(__FILE__)) . "/icon-streetview.png";
	
	$link_markup = "<a href='{$simple_streetview_upload_iframe_src}&amp;tab=simple_streetview&amp;TB_iframe=true&amp;height=400&amp;width=640' class='thickbox' title='$simple_streetview_title'><img src='$logo' alt='$logo' /></a>\n";

	echo $link_markup;
}

function streetview_create_iframe() {
	wp_iframe('streetview_inner_custom_box');
}

/* Prints the inner fields for the custom post/page section */
function streetview_inner_custom_box() {
	//media_upload_header();
	
	?>
	<div style="padding: 15px;">
		<h3 class="media-title">Add Google Street View panorama</h3>
		<p class="howto">Use the searchbox or manual panning to find you location. Drag the yellow icon to a blue area on the map. When the Street View window appears, pan and zoom till you see what you want and finish by clicking &quot;Add this view&quot;.</p>
		<input id="streetview_address" type="text" name="address" value="street, city" />&nbsp;<input type="submit" name="geocode_button" value="Show on map" onclick="streetview_findaddress()"/><br />
		<div id="streetview_canvas" style="width: 620px; height: 300px"></div>
		<br/>
		<input class="button" style="font-weight: bold;" value="Add this view" type="button" onclick="javascript:streetview_getthepov();">
	</div>
	<script type="text/javascript">
		var map;
		var geocoder;
		function streetview_initialize() {
		  var center = new google.maps.LatLng(50, -50);
		  var mapOptions = {
			center: center,
			zoom: 2,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			streetViewControl: true
		  };
		  geocoder = new google.maps.Geocoder();
		  map = new google.maps.Map(document.getElementById("streetview_canvas"), mapOptions);
		}
		
		function streetview_getthepov() {
			var pano = map.getStreetView();
			var pov = pano.getPov();
			
			if (pos = pano.getPosition()) {
				
				
				var embedcode = "[streetview width=\"100%\" height=\"250px\" lat=\""+pos.lat()+"\" lng=\""+pos.lng()+"\" heading=\""+pov.heading+"\" pitch=\""+pov.pitch+"\" zoom=\""+pov.zoom+"\"][/streetview]<br/>";
								
				top.send_to_editor(embedcode);
			} else {
				alert('Drag and drop the yellow icon to a place first!');
			}
		}
		
		// based on google's geocode example code	
		function streetview_findaddress() {
			var address = document.getElementById("streetview_address").value;
			geocoder.geocode( { 'address': address}, function(results, status) {
			  if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				map.setZoom(15);
			  } else {
				alert("Geocode was not successful for the following reason: " + status);
			  }
			});
		}
				
		streetview_initialize();
		
	</script>
	
	<?php
	
}

?>