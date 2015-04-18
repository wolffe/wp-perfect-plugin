<?php
/*
Plugin Name: WordPress Perfect Plugin
Plugin URI: http://getbutterfly.com/wordpress-plugins/wordpress-perfect-plugin/
Description: Perfect Plugin aims to provide the minimum options for any starter or advanced webmaster. Perfect Plugin has basic options for search engines, analytics, easy code insertion, a simple contact form, Google Maps and StreetView and many other useful functions and shortcodes.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/
Version: 0.1.9

WordPress Perfect Plugin
Copyright (C) 2010, 2011, 2012, 2013, 2014, 2015 Ciprian Popescu (getbutterfly@gmail.com)

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

//
define('W3P_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('W3P_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('W3P_VERSION', '0.1.9');
//

// plugin localization
$plugin_dir = basename(dirname(__FILE__)); 
load_plugin_textdomain('w3p', false, $plugin_dir . '/languages'); 
//

// Begin Code
add_option('w3p_email', '');

function w3p_plugin_menu() {
	add_menu_page('Perfect Plugin', 'Perfect Plugin', 'manage_options', 'w3p', 'w3p_plugin_main', W3P_PLUGIN_URL.'/images/icon-16.png');
	add_submenu_page('w3p', 'W3P Webmaster', 'W3P Webmaster', 'manage_options', 'w3p-webmaster', 'all_in_one_webmaster_options_page');
	add_submenu_page('w3p', 'W3P Options', 'W3P Options', 'manage_options', 'w3p-options', 'w3p_plugin_options');
	add_submenu_page('w3p', 'W3P Media Sitemap', 'W3P Media Sitemap', 'manage_options', 'w3p-sitemap', 'multi_sitemap_generate');
	add_submenu_page('w3p', 'W3P Sweeper', 'W3P Sweeper', 'manage_options', 'w3p-sweeper', 'w3p_sweeper');
}

function add_w3p_additional_css() {
	echo '<link rel="stylesheet" href="'.W3P_PLUGIN_URL.'/css/additional.css" type="text/css" />';
	echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
}

add_action('wp_head', 'add_w3p_additional_css');
add_action('admin_menu', 'w3p_plugin_menu');
add_action('wp_dashboard_setup', 'w3p_add_dashboard_widgets'); // dashboard news, right from thge horse's mouth

function w3p_plugin_main() {
	if(!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Perfect Plugin (W3P)</h2>
		<div id="poststuff" class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3>About Perfect Plugin (W3P) <small>(<a href="http://getbutterfly.com/" rel="external">official web site</a>)</small></h3>
				<div class="inside">
					<p><small>You are using Perfect Plugin version <strong><?php echo W3P_VERSION; ?></strong>.</small></p>
					<p><img src="<?php echo W3P_PLUGIN_URL; ?>/images/icon-32.png" alt="" class="alignright">Perfect Plugin (W3P) aims to provide the minimum options for any starter or advanced webmaster. Perfect Plugin has basic options for search engines, analytics, easy code insertion, a simple contact form, Google Maps and StreetView and many other useful functions and shortcodes.</p>

                    <p>For support, feature requests and bug reporting, please visit the <a href="//getbutterfly.com/" rel="external">official website</a>.</p>
                    <p>&copy;<?php echo date('Y'); ?> <a href="//getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <a   href="//getbutterfly.com/forums/" rel="external">Support forums</a> &middot; <a href="//getbutterfly.com/trac/" rel="external">Trac</a> &middot; <a href="http://outdatedbrowser.com/en">Upgrade your browser today!</a> &middot; <small>Code wrangling since 2005</small></p>
				</div>
			</div>
		</div>

		<div id="poststuff">
			<div class="postbox">
				<h3>Available Modules</h3>
				<div class="inside">
		<ul>
			<li><strong>Webmaster Settings</strong> - A complete solution for your webmaster <code>meta</code> keys, verifications and analytics needs. Migrates data from AIO Webmaster plugin. Uses the latest Google Analytics tracking code.</li>
			<li><strong>Child Redirect</strong> - This module does a 301 redirect on top-level parent pages to their first child page, based first on menu order, then post title if no menu order is set.</li>
		</ul>
		<h3>Current Shortcodes</h3>
		<ul>
			<li><strong>List Subpages</strong> - Use the <code>[subpages]</code> shortcode that lists the sub pages of the current page as a <code>ul/li</code> list, allowing you to use parent pages in a similar way to categories. The <code>ul</code> structure is ready for styling using this class - <code>&lt;ul class=&quot;w3p-subpages&quot;&gt;</code>.</li>
			<li><strong>Contact Form</strong> - Use the <code>[pp_contact_form]</code> shortcode in a post or a page to display a simple contact form that just works.</li>
			<li>
				<strong>Google Maps</strong> - Use the following shortcode (with the desired parameters) in a post or a page to display a Google map:<br /><br />
				<code>[<strong>map</strong> <strong>id</strong>=&quot;map3&quot; <strong>w</strong>=&quot;200&quot; <strong>h</strong>=&quot;100&quot; <strong>z</strong>=&quot;5&quot; <strong>maptype</strong>=&quot;TERRAIN&quot; <strong>lat</strong>=&quot;34&quot; <strong>lon</strong>=&quot;-118&quot; <strong>address</strong>=&quot;Tokyo, Japan&quot; <strong>marker</strong>=&quot;yes&quot; <strong>markerimage</strong>=&quot;http://code.google.com/apis/maps/documentation/javascript/examples/images/beachflag.png&quot; <strong>infowindow</strong>=&quot;&lt;strong&gt;Hello World&lt;/strong&gt;&quot; <strong>traffic</strong>=&quot;yes&quot; <strong>kml</strong>=&quot;http://gmaps-samples.googlecode.com/svn/trunk/ggeoxml/cta.kml&quot;]</code><br /><br />
				Default value = <strong>ROADMAP</strong> | Accepted values = ROADMAP | SATELLITE | HYBRID | TERRAIN // <strong>lat</strong> and <strong>lon</strong> parameters are optional. <strong>address</strong> parameter is mandatory.
			</li>
			<li><strong>Google Streetview</strong> - Use the StreetView editor button (<img src="<?php echo W3P_PLUGIN_URL;?>/modules/icon-streetview.png" alt="" />) to open a popup and add your address.</li>
			<li><strong>SEO Love</strong> - Add the <code>[seo_love]</code> shortcode to any post or page to display the search bar, or add the <code>&lt;?php echo seo_love();?&gt;</code> PHP function to your blog template. The plugin allows the author/user to search for the post title on the major search engines, Google, Bing and Ask. The purpose of this search is to check the competition for any given title, or to check the indexation for any given post.</li>
		</ul>



		<h3>Security Suggestions</h3>
		<p>http://perishablepress.com/5g-firewall-beta/</p>
				</div>
			</div>
		</div>

	</div>
<?php
}

function w3p_plugin_options() {
	if(isset($_POST['saveMe'])) {
		$w3p_email = $_POST['w3p_email'];

		update_option('w3p_email', $w3p_email);

		// clean up old options
		delete_option('w3p_feedburner');
		delete_option('feedburner_settings');
		?>
		<div class="updated"><p><strong>Settings saved.</strong></p></div>
		<?php
	}

	// read in existing option value from database
    $option_value_w3p_email = get_option('w3p_email');
	$w3p_email = get_option('w3p_email');
	if($w3p_email == '')
		$w3p_email = 'none';
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>Perfect Settings</h2>
		<div id="poststuff" class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3><?php _e('WordPress Settings', 'w3p'); ?></h3>
				<div class="inside">
					<form name="form1" method="post" action="">
						<p>
							Use the <code>[pp_contact_form]</code> shortcode in a post or a page to display a simple contact form that just works.<br>
							<input type="text" name="w3p_email" id="w3p_email" value="<?php echo $option_value_w3p_email; ?>" class="regular-text"> <label for="w3p_email">Contact Form Email</label>
							<br>
							<small>Contact emails will be sent to this address (currently set to <strong><?php echo $w3p_email; ?></strong>).</small>
						</p>
						<h3>Modules</h3>
						<p>No modules available.</p>
						<p class="submit">
							<input type="submit" name="saveMe" class="button-primary" value="Save Changes" />
						</p>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php
}

function w3p_sweeper() {
	include('modules/w3p-sweeper.php');
}

// Security modules // built-in
/*
Plugin Name: Block Bad Queries
Description: Protect WordPress Against Malicious URL Requests
*/
if(strpos($_SERVER['REQUEST_URI'], "eval(") || strpos($_SERVER['REQUEST_URI'], "CONCAT") || strpos($_SERVER['REQUEST_URI'], "UNION+SELECT") || strpos($_SERVER['REQUEST_URI'], "base64")) {
	@header("HTTP/1.1 400 Bad Request");
	@header("Status: 400 Bad Request");
	@header("Connection: Close");
	@exit;
}
// End security modules

// Begin modules
include('modules/w3p-wordpress.php');
include('modules/w3p-basic-settings.php');
include('modules/w3p-list-subpages.php');
include('modules/w3p-contact-form.php');
include('modules/w3p-google-streetview.php');
include('modules/w3p-google-maps.php');

include('modules/w3p-sitemap.php');

include('modules/w3p-misc.php');

include('modules/w3p-dashboard-widget.php');
?>
