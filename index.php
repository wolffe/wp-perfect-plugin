<?php
/*
Plugin Name: WordPress Perfect Plugin
Plugin URI: http://getbutterfly.com/wordpress-plugins/wordpress-perfect-plugin/
Description: Perfect Plugin aims to provide the minimum options for any starter or advanced webmaster. Perfect Plugin has basic options for search engines, analytics, easy code insertion, a simple contact form, Google Maps and StreetView and many other useful functions and shortcodes.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/
Version: 0.1.6

WordPress Perfect Plugin
Copyright (C) 2010, 2011, 2012, 2013 Ciprian Popescu (getbutterfly@gmail.com)

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
define('W3P_VERSION', '0.1.6');
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
	add_submenu_page('w3p', 'W3P SEO Tracker', 'W3P SEO Tracker', 'manage_options', 'w3p-seo', 'w3p_seo_options');
	add_submenu_page('w3p', 'W3P Media Sitemap', 'W3P Media Sitemap', 'manage_options', 'w3p-sitemap', 'multi_sitemap_generate');

	if(get_option('w3p_analytics_run') == 1)
		add_submenu_page('w3p', 'W3P Analytics', 'W3P Analytics', 'manage_options', __FILE__, 'iriwpsa');
}

function add_w3p_additional_css() {
	echo '<link rel="stylesheet" href="'.W3P_PLUGIN_URL.'/css/additional.css" type="text/css" />';
	echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
}

add_action('wp_head', 'add_w3p_additional_css');
add_action('admin_menu', 'w3p_plugin_menu');

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
					<p>
						<?php _e('Related Sites:', 'w3p'); ?> 
						<a href="http://getbutterfly.com/">getButterfly</a> | 
						<a href="http://roo.ie/">Roo.ie</a> | 
						<a href="http://www.demain.ie/">Demain Technologies</a> | 
						<a href="http://browsehappy.com/">Browse Happy</a> <small>:) Upgrade your browser today!</small>
					</p>
					<p>
						<?php _e('By the Same Author:', 'w3p'); ?> 
						<a href="http://getbutterfly.com/wordpress-plugins/portable-phpmyadmin/">Portable phpMyAdmin</a> <sup><small>FREE</small></sup> | 
						<a href="http://getbutterfly.com/wordpress-plugins/smartbackup/">Smart Backup</a> <sup><small>COMMERCIAL</small></sup> | 
						<a href="http://getbutterfly.com/wordpress-plugins/"><small>Even More WordPress Plugins</small></a>
					</p>
				</div>
			</div>
		</div>

		<div id="poststuff" class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3>What is W3P?</h3>
				<div class="inside">
					<img src="<?php echo W3P_PLUGIN_URL; ?>/images/icon-32.png" alt="" class="alignright">
					<p>Perfect Plugin (W3P) aims to provide the minimum options for any starter or advanced webmaster. Perfect Plugin has basic options for search engines, analytics, easy code insertion, a simple contact form, Google Maps and StreetView and many other useful functions and shortcodes.</p>
				</div>
			</div>
		</div>

		<div id="poststuff" class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3>Available Modules</h3>
				<div class="inside">
		<ul>
			<li><strong>Webmaster Settings</strong> - A complete solution for your webmaster <code>meta</code> keys, verifications and analytics needs. Migrates data from AIO Webmaster plugin. Uses the latest Google Analytics tracking code.</li>
			<li><strong>Child Redirect</strong> - This module does a 301 redirect on top-level parent pages to their first child page, based first on menu order, then post title if no menu order is set.</li>
			<li><strong>SEO/SERP</strong> - This module features a SEO/SERP tracker for various ranks and backlinks. Useful to keep track of site SEO progress.</li>
			<li><strong>Analytics</strong> - This module, a highly improved fork of StatPress, shows you real time statistics of your site. It collects information about visitors, spiders, search keywords, feeds, browsers, OS etc. Once active, it immediately starts to collect information.</li>
* %thistotalvisits% - this page, total visits
* %since% - Date of the first hit
* %visits% - Today visits
* %totalvisits% - Total visits
* %os% - Operative system
* %browser% - Browser
* %ip% - IP address
* %visitorsonline% - Counts all online visitors
* %usersonline% - Counts logged online visitors
* %toppost% - The most viewed Post
* %topbrowser% - The most used Browser
* %topos% - The most used O.S.
* %thistotalpages% - Total pageviews so far
* %pagestoday% - Pageviews today
* %pagesyesterday% - Pageviews yesterday
* %latesthits%		</ul>
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
		$w3p_analytics_run = $_POST['w3p_analytics_run'];

		update_option('w3p_email', $w3p_email);
		update_option('w3p_analytics_run', $w3p_analytics_run);

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
						<p>
							<select name="w3p_analytics_run">
								<option value="1"<?php if(get_option('w3p_analytics_run') == '1') echo ' selected="selected"';?>>Enable analytics module</option>
								<option value="0"<?php if(get_option('w3p_analytics_run') == '0') echo ' selected="selected"';?>>Disable analytics module</option>
							</select> 
							<br /><small>Disable this module if it causes high load to your server.</small>
						</p>
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

include('modules/w3p-seo.php');
include('modules/w3p-sitemap.php');

include('modules/w3p-misc.php');

// Begin other
if(get_option('w3p_analytics_run') == 1)
	include('mod-analytics.php');


// Native WP pagination function
/*
The function takes an array of parameters that make it versatile enough to use for any kind of paging:

    base
    This is the path for the page number links, not including the pagination-specific part of the URL. The characters %_% will be substituted in that URL for the page-specific part of the URL.
    format
    This is the "page" part of the URL. %#% is substituted for the page number. For example, page/%#% or ?page=%#%.
    total
    The total number of pages available.
    current
    The current page number.
    show_all
    Lists all page links, instead of limiting it to a certain number of links to the left and right of the current page.
    prev_next
    Includes the "Previous" and "Next" links (if applicable), just as you might normally do with the previous_posts_link() function.
    prev_text and next_text
    Text to put inside the "Previous" and "Next" links.
    end_size
    The number of page links to show at the end. Defaults to 1 (e.g. 1 2 3 … 10).
    mid_size­
    The number of pages to show on either side of the current page. Defaults to 2 (example: 1 … 3 4 5 6 7 … 10).
    type
    Allows you to specify an output style. The default is "plain," which is just a string of links. Can also be set to list (i.e. ul and li representation of links) and array (i.e. returns an array of page links to be potentially outputted any way you like in code).
    You can also add query arguments and fragments.

It will generate this:

<ul class='page-numbers'>
     <li><span class='page-numbers current'>1</span></li>
     <li><a class='page-numbers' href='http://mysite.com/page/2/'>2</a></li>
     <li><a class='page-numbers' href='http://mysite.com/page/3/'>3</a></li>
     <li><a class='page-numbers' href='http://mysite.com/page/4/'>4</a></li> 
     <li><a class='page-numbers' href='http://mysite.com/page/5/'>5</a></li>
     <li><span class='page-numbers dots'>...</span></li>
     <li><a class='page-numbers' href='http://mysite.com/page/10/'>10</a></li>
     <li><a class='next page-numbers' href='http://mysite.com/page/2/'>Next &raquo;</a></li>
</ul>

*/
/*
// get total number of pages
global $wp_query;
$total = $wp_query->max_num_pages;
// only bother with the rest if we have more than 1 page!
if ( $total > 1 )  {
     // get the current page
     if ( !$current_page = get_query_var('paged') )
          $current_page = 1;
     // structure of "format" depends on whether we're using pretty permalinks
     $format = empty( get_option('permalink_structure') ) ? '&page=%#%' : 'page/%#%/';
     echo paginate_links(array(
          'base' => get_pagenum_link(1) . '%_%',
          'format' => $format,
          'current' => $current_page,
          'total' => $total,
          'mid_size' => 4,
          'type' => 'list'
     ));
}
*/

/* Get included files // useful for debugging
$included_files = get_included_files();

foreach ($included_files as $filename) {
    echo $filename . '<br>';
}



if (isset($variable)) {}
if (function_exists('function_name')) {}
if (class_exists('class_name')) {}
*/
?>
