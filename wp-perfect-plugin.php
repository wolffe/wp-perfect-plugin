<?php
/*
Plugin Name: WP Perfect Plugin
Plugin URI: https://getbutterfly.com/
Description: WP Perfect Plugin aims to provide advanced options for any web developer. WP Perfect Plugin has options for search engines, such as ownership verification, local business JSON-LD data, Open Graph, analytics, header and footer easy code insertion and optimised SEO defaults.
Author: Ciprian Popescu
Author URI: https://getbutterfly.com/
Version: 1.4.5
Text Domain: wp-perfect-plugin

WP Perfect Plugin
Copyright (C) 2010-2019 Ciprian Popescu (getbutterfly@gmail.com)
Copyright (C) 2010-2011 Crunchify (http://crunchify.com/)

Parts of this plugin have used code from All in One Webmaster plugin (https://wordpress.org/plugins/all-in-one-webmaster/) for migration purposes.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

if (!defined('ABSPATH')) {
    exit;
}

define('W3P_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('W3P_PATH', plugin_dir_path(__FILE__));

include 'modules/w3p-settings.php';
include 'modules/w3p-list-subpages.php';
include 'modules/w3p-search-console.php';

function w3p_plugin_menu() {
	add_options_page(__('WP Perfect Plugin', 'wp-perfect-plugin'), __('WP Perfect Plugin', 'wp-perfect-plugin'), 'manage_options', 'w3p', 'w3p_settings');
}

add_action('admin_menu', 'w3p_plugin_menu');

add_action('admin_enqueue_scripts', 'w3p_enqueue_scripts');
function w3p_enqueue_scripts() {
    wp_enqueue_style('gbad', plugins_url('css/gbad.css', __FILE__));
}

add_action('init', 'w3p_add_excerpts_to_pages');



/**
 * Generate sitemap.xml in document root
 *
 * @return void
 */
function w3p_create_sitemap() {
    $w3pSitemapTypes = get_option('w3p_sitemap_types');

    $postsForSitemap = get_posts([
        'numberposts' => -1,
        'orderby' => 'modified',
        'post_type' => $w3pSitemapTypes,
        'order' => 'DESC',
        'post_status' => 'publish',
        'suppress_filters' => true,
        'ignore_sticky_posts' => true,
        'no_found_rows' => true,
        'cache_results' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
    ]);

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

    foreach ($postsForSitemap as $post) {
        setup_postdata($post);

        $postdate = explode(' ', $post->post_modified);

        $sitemap .= "\t" . '<url>' . "\n" .
                    "\t\t" . '<loc>' . get_permalink($post->ID) . '</loc>' . "\n" .
                    "\t\t" . '<lastmod>' . $postdate[0] . '</lastmod>' . "\n" .
                    "\t" . '</url>' . "\n";
    }

    $sitemap .= '</urlset>';

    $fp = fopen(ABSPATH . 'sitemap.xml', 'w');

    fwrite($fp, $sitemap);
    fclose($fp);
}

add_action('publish_post', 'w3p_create_sitemap');
add_action('publish_page', 'w3p_create_sitemap');
add_action('save_post', 'w3p_create_sitemap');
