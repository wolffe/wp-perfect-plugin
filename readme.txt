=== WP Perfect Plugin ===
Contributors: butterflymedia
Tags: seo, search console, open graph, local, sem, serp, google, bing, yandex
Requires at least: 4.9
Requires PHP: 7.0
Tested up to: 5.3
Stable tag: 1.3.12
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

WP Perfect Plugin (W3P) provides the minimum SEO/SEM/local/marketing options for any site owner.

== Description ==

W3P has options for search engines, such as ownership verification, local business JSON-LD data, Open Graph, analytics, header and footer easy code insertion and optimised SEO defaults.

W3P also features a subpages shortcode.

For support, feature requests and bug reporting, please visit the [official website](https://getbutterfly.com/wordpress-plugins/ "getButterfly").

== Installation ==

Upload and activate the plugin.

== Screenshots ==

1. Search Engine Verification And Link Relationships
2. Homepage Details
3. Local Business Details
4. Analytics and Tag Management
5. Header/Footer Management
6. Open Graph
7. Miscellaneous
9. Plugin Dashboard

== Changelog ==

= 1.3.12 =
* UPDATE: Updated WordPress compatibility
* UPDATE: Incremental testing GIT2SVN

= 1.3.6 =
* UPDATE: Updated help text for custom homepage description
* DEVELOPMENT: Restarted development on GitHub

= 1.3.5 =
* UPDATE: Removed Google+ integration
* UPDATE: Updated PHP requirements
* UPDATE: Updated WordPress compatibility

= 1.3.4 =
* FIX: Removed unused shortcode parameter
* UPDATE: Updated WordPress compatibility

= 1.3.3 =
* UPDATE: Updated string/variable sanitisation

= 1.3.2 =
* UPDATE: Updated plugin name to avoid confusion
* FIX: Fixed readme.txt version

= 1.3.1 =
* UPDATE: Added Baidu verification tag
* UPDATE: Updated Google Tag Manager loading priority
* UPDATE: Removed Google Tag Manager (noscript)

= 1.3.0 =
* UPDATE: Updated WordPress compatibility
* FIX: Added missing Google Analytics and Google Tag Manager tags
* FIX: Added missing header and footer tags
* FIX: Added missing content

= 1.2.1 =
* UPDATE: Updated WordPress compatibility
* FIX: Removed unused files and functions
* FIX: Removed deprecated links
* FIX: Added missing styles (backend)

= 1.2 =
* FIX: Removed double variable declaration
* FIX: Removed unused (inherited) variable declaration
* UPDATE: Moved plugin to Settings area to unclutter menu
* UPDATE: Removed unused welcome mat feature
* UPDATE: UI tweaks
* PERFORMANCE: Removed unused code, fixed line endings and added PSR code changes

= 1.1.3 =
* FEATURE: Added microdata breadcrumbs (unstyled)

= 1.1.2 =
* FIX: Google Tag Manager script position

= 1.1.1 =
* FIX: Fixed excerpt not being generated from the post/page content
* FIX: Fixed settings page description

= 1.1.0 =
* FEATURE: Added welcome mat (scroll mat) feature
* FIX: Fixed performance

= 1.0.4 =
* FIX: Unified gbad.css styles (.codor)
* UPDATE: Removed Google Maps as it now requires a key and a developer account

= 1.0.3 =
* UI: UI and wording tweaks
* UI: Added sharing debugger link for Open Graph/Facebook
* FIX: Added OG schema to DOCTYPE only if Open Graph option is checked
* FIX: Fixed excerpts for SEO description
* FIX: Fixed DOCTYPE prefix for Open Graph
* UPDATE: Added OG default image option
* UPDATE: Added post image capture if no default image or post thumbnail is set
* UPDATE: Added excerpts to pages

= 1.0.2 =
* FIX: Fixed an issue with description improperly escaping quotes
* FIX: Removed version, path and URL constants
* FIX: Removed globally set option
* FIX: Code cleanup
* UPDATE: UI tweaks and section links
* UPDATE: readme.txt improvements
* FEATURE: Added Majestic SEO verification

= 1.0.1 =
* UPDATE: Changed several option names to better reflect the plugin

= 1.0.0 =
* FIX: Removed unsafe built-in security module
* FIX: Better plugin security
* FIX: Removed unused /languages/ directory
* FIX: Renamed some functions to avoid conflicts
* FIX: Removed old Google Maps JS API
* FIX: Added capability check for option saving
* FIX: Removed deprecated contact form feature

= 0.6.4 =
* FIX: Removed tags from og:title content
* FIX: Fixed old support link

= 0.6.3 =
* FIX: Fixed community translations for WordPress 4.6+
* FIX: Fixed several label targets
* UPDATE: Added screenshots

= 0.6.2 =
* FIX: Fixed wrong translatable string
* FIX: Removed unused sitemap module

= 0.6.1 =
* FIX: Fixed local business contextual help
* FIX: Fixed code standards
* FIX: Fixed scripts and styles enqueue
* UPDATE: Added more translatable strings

= 0.6.0 =
* UPDATE: Added proper i18n

= 0.5.3 =
* FIX: Added missing module

= 0.5.2 =
* UPDATE: Removed old files

= 0.5.1 =
* UPDATE: File cleanup and description update

= 0.5.0 =
* UI: UI/UX improvements
* FEATURE: Removed Alexa verification as site claiming has been deprecated
* FEATURE: Added Yandex verification
* FEATURE: Added Pinterest verification
* FEATURE: Added Web of Trust verification
* FEATURE: Added Google+ link relationships (profile URL and page URL)
* FEATURE: Added Twitter link relationship
* FEATURE: Added Google Tag Manager
* FEATURE: Added local business details
* UPDATE: Removed several theme-specific and opinionated defaults
* UPDATE: Removed dashboard widget

= 0.4.0 =
* FIX: Fixed dashboard beacon
* UPDATE: Removed Compete analytics
* UPDATE: Renamed and tweaked the UI and wording of the Webmaster section
* UPDATE: Updated messages and notifications
* UPDATE: Removed 404 redirection as WordPress does the job using canonical redirection
* UPDATE: Removed Google Streetview as it was dependent to an API key
* UPDATE: Removed Yahoo and Ask in SEO Love module
* PERFORMANCE: Added autoloading to options

= 0.3.0 =
* UPDATE: Added getButterfly ad box

= 0.2 =
* UPDATE: Removed Sweeper plugin
* UPDATE: Removed obsolete analytics module
* UPDATE: Removed obsolete sitemap module
* UPDATE: Removed obsolete admin.css
* FIX: Fixed several links
* FIX: Fixed Google Maps URL to automatically pick http:// or https://
* FIX: Added language files

= 0.1.9 =
* FEATURE: Merged WordPress Sweeper plugin
* UPDATE: Removed obsolete SEO tracker module

= 0.1.8 =
* UPDATE: Removed obsolete analytics module
* FIX: Removed old links

= 0.1.7 =
* Fixed Google Streetview (backend)
* Reformatted SEO tracker
* Reformatted plugin dashboard page
* Added dashboard "planet" widget for getbutterfly.com

= 0.1.6.2 =
* Removed deprecated functions from mod-analytics.php
* Fixed unset POST variables in w3p-sitemap.php
* Fixed version number in index.php
* TODO: Include a native WordPress deprecation checker
* TODO: Include a malware check (root string finder)

= 0.1.6.1 =
* Removed empty forms from SEO Tracker
* Removed an inactive function

= 0.1.6 =
* Removed custom login logo (WordPress 3.6 changed the format)
* Removed an erroneus login screen customization
* Removed an inactive function
* Removed Feedburner options as the plugin is outdated, and there was no update from Google for almost 3 years
* Removed all custom dashboard CSS code

= 0.1.5.3 =
* Added media sitemap module
* Added license specification

= 0.1.5.2 =
* Tweaked the Google Maps module (paragraph tag insertion issue)
* Tweaked the Analytics module to work from inside the Perfect Plugin
* Fixed deprecated functions

= 0.1.5.1 =
* Added option to enable/disable analytics module
* Consolidated the SEO tracker
* Merged several options scattered throughout the plugin

= 0.1.5 =
* Merged with SEO Love plugin
* Merged with Smashing Analytics plugin (update/migration is possible)
* Removed hardcoded path

= 0.1.4.2 =
* Added additional (override) style for pages (Google Maps inner images background)
* Improved page speed by removing Google Maps API calls on pages without Google Maps or StreetView
* Removed a getButterfly link
* Removed an empty header call

= 0.1.4.1 =
* Removed Yahoo Site Explorer as it's no longer relevant

= 0.1.4 =
* Official public release

= 0.1.2.7 =
* Removed a duplicate option
* Fixed Google PageRank URL due the October 2011 algorythm change (changed parameter "search" to "tbr")
* Simplified the SEO Tracker module

= 0.1.2.6 =
* Removed Blog Catalog code
* Removed more useless code from webmaster panel
* Removed warning

= 0.1.2.5 =
* Fixed Bing/Yahoo switched codes

= 0.1.2.4 =
* Added gallery fix
* Fixed Google Streetview icon
* Removed RSS dashboard widget

= 0.1.2.2 =
* Added more custom branding
* Fixed a Google Feedburner warning

= 0.1.2.1 =
* Added page speed tracking for Google Analytics
* Added custom login panel

= 0.1.2 =
* Rewritten basic webmaster settings

= 0.1.1 =
* Added submenus
* Added an options page (currently with email address only)

= 0.1.0 =
* First release (buggy, crippled and alpha quality)

## Upgrade Notice ##

### 1.0.1 ###
1.0 is a major update. You will need to recheck/reset your verification tags and use a different contact plugin when upgrading from 0.6.x to 1.x. The contact module has been retired due to several security issues.
