<?php
/*
Plugin Name: WordPress Perfect Plugin
Plugin URI: http://www.blogtycoon.net/wordpress-plugins/wordpress-perfect-plugin/
Description: Perfection has no description.
Author: Ciprian Popescu
Author URI: http://www.blogtycoon.net/
Version: 0.1.2

WordPress Perfect Plugin
Copyright (C) 2010, 2011 Ciprian Popescu

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

error_reporting(0); // Used for debug
// w3p is wppp - wordpress perfect plugin - 3 p's // get it? // :|

//
if(!defined('WP_CONTENT_URL'))
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if(!defined('WP_PLUGIN_URL'))
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if(!defined('WP_CONTENT_DIR'))
	define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if(!defined('WP_PLUGIN_DIR'))
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

define('W3P_PLUGIN_URL', WP_PLUGIN_URL.'/wp-perfect-plugin');
define('W3P_PLUGIN_PATH', WP_PLUGIN_DIR.'/wp-perfect-plugin');
define('W3P_VERSION', '0.1.2');
//

// Begin Code
add_option('w3p_email', '', '', 'no');

function w3p_plugin_menu() {
	add_menu_page('Perfect Plugin', 'Perfect Plugin', 'manage_options', 'w3p', 'w3p_plugin_main', W3P_PLUGIN_URL.'/images/icon-16.png');
	add_submenu_page('w3p', 'W3P Webmaster', 'W3P Webmaster', 'manage_options', 'w3p-webmaster', 'all_in_one_webmaster_options_page');
	add_submenu_page('w3p', 'W3P Options', 'W3P Options', 'manage_options', 'w3p-options', 'w3p_plugin_options');
	add_submenu_page('w3p', 'W3P Feed Options', 'W3P Feed Options', 'manage_options', 'w3p-feedburner', 'ol_feedburner_options_subpanel');
	add_submenu_page('w3p', 'W3P SEO', 'W3P SEO', 'manage_options', 'w3p-seo', 'w3p_seo_options');
}

function add_w3p_admin_css() {
	echo '<link type="text/css" rel="stylesheet" href="'.W3P_PLUGIN_URL.'/css/admin.css" />'."\n";
}

add_action('admin_head', 'add_w3p_admin_css');
add_action('admin_menu', 'w3p_plugin_menu');

function w3p_plugin_main() {
	if(!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Perfect Plugin Settings</h2>
		<p>Perfect Plugin aims to provide the minimum options for any starter or advanced webmaster. Perfect Plugin has basic options for search engines, analyics, easy code insertion, a simple contact form, Google Maps and StreetView and many other useful functions and shortcodes.</p>
		<p><small>You are using Perfect Plugin version <strong><?php echo W3P_VERSION;?></strong>.</small></p>
		<img src="<?php echo W3P_PLUGIN_URL;?>/images/icon-32.png" alt="" />
		<h3>Child Redirection</h3>
		<p>This module does a 301 redirect on top-level parent pages to their first child page, based first on menu order, then post title if no menu order is set.</p>
		<hr />
		<h3>Google Maps</h3>
		<p>Use the following shortcode (with the desired parameters) in a post or a page to display a Google map:</p>
		<p><code>[<strong>map</strong> <strong>id</strong>=&quot;map3&quot; <strong>w</strong>=&quot;200&quot; <strong>h</strong>=&quot;100&quot; <strong>z</strong>=&quot;5&quot; <strong>maptype</strong>=&quot;TERRAIN&quot; <strong>lat</strong>=&quot;34&quot; <strong>lon</strong>=&quot;-118&quot; <strong>address</strong>=&quot;Tokyo, Japan&quot; <strong>marker</strong>=&quot;yes&quot; <strong>markerimage</strong>=&quot;http://code.google.com/apis/maps/documentation/javascript/examples/images/beachflag.png&quot; <strong>infowindow</strong>=&quot;&lt;strong&gt;Hello World&lt;/strong&gt;&quot; <strong>traffic</strong>=&quot;yes&quot; <strong>kml</strong>=&quot;http://gmaps-samples.googlecode.com/svn/trunk/ggeoxml/cta.kml&quot;]</code></p>
		<p>Default value = <strong>ROADMAP</strong> | Accepted values = ROADMAP | SATELLITE | HYBRID | TERRAIN</p>
		<p><strong>lat</strong> and <strong>lon</strong> parameters are optional. <strong>address</strong> parameter is mandatory.</p>
		<hr />
		<h3>Contact Form</h3>
		<p>Use the <code>[pp_contact_form]</code> shortcode in a post or a page to display a simple contact form that just works.</p>
		<hr />
	</div>
<?php
}

function w3p_plugin_options() {
	$hidden_field_name = 'w3p_submit_hidden';
	$w3p_email_field_name = 'w3p_email';

	// read in existing option value from database
    $option_value_w3p_email = get_option('w3p_email');
	$w3p_email = get_option('w3p_email');

    // See if the user has posted us some information // if they did, this hidden field will be set to 'Y'
	if(isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
		$option_value_w3p_email = $_POST[$w3p_email_field_name];

		update_option('w3p_email', $option_value_w3p_email);
		?>
		<div class="updated"><p><strong>Settings saved.</strong></p></div>
		<?php
	}
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Perfect Settings</h2>

		<form name="form1" method="post" action="">
			<input type="hidden" name="<?php echo $hidden_field_name;?>" value="Y" />
			<p>
				<input type="text" name="<?php echo $w3p_email_field_name;?>" id="<?php echo $w3p_email_field_name;?>" value="<?php echo $option_value_w3p_email;?>" size="40" /> <label for="<?php echo $w3p_email_field_name;?>">Contact Form Email</label>
				<br />
				<span class="description"><small>Contact emails will be sent to this address (currently set to <strong><?php echo $w3p_email;?></strong>).</small></span>
			</p>
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="Save Changes" />
			</p>
		</form>
	</div>
<?php
}

// Begin modules
include('modules/w3p-feedburner.php');
include('modules/w3p-basic-settings.php');
include('modules/w3p-child-redirect.php');
//include('modules/w3p-list-subpages.php');
include('modules/w3p-contact-form.php');
include('modules/w3p-google-streetview.php');
include('modules/w3p-google-maps.php');

include('modules/w3p-seo-rank.php');
include('modules/w3p-seo.php');
?>
