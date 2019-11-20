<?php
if (!defined('ABSPATH')) {
    exit;
}

function w3p_settings() { ?>
	<div class="wrap">
		<h2><?php esc_html_e('WP Perfect Plugin Settings', 'wp-perfect-plugin'); ?></h2>

        <?php $tab = isset($_GET['tab']) ? (string) sanitize_text_field($_GET['tab']) : 'dashboard'; ?>

        <h2 class="nav-tab-wrapper">
            <a href="<?php echo admin_url('admin.php?page=w3p&amp;tab=dashboard'); ?>" class="nav-tab <?php echo $tab === 'dashboard' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Dashboard', 'wp-perfect-plugin'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=w3p&amp;tab=settings'); ?>" class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('General Settings', 'wp-perfect-plugin'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=w3p&amp;tab=console'); ?>" class="nav-tab <?php echo $tab === 'console' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Search Engine Console', 'wp-perfect-plugin'); ?></a>
        </h2>

        <?php if ((string) $tab === 'dashboard') { ?>
            <div id="poststuff">
                <div class="postbox">
                    <h2><span class="dashicons dashicons-lightbulb"></span> <?php _e('About WP Perfect Plugin', 'wp-perfect-plugin'); ?></h2>
                    <div class="inside">
                        <p>WP Perfect Plugin aims to provide advanced options for any web developer.</p>

                        <p>For support, feature requests and bug reporting, please visit the <a href="https://getbutterfly.com/" rel="external">official website</a>. If you enjoy this plugin, don't forget to rate it. Also, try our other WordPress plugins at <a href="https://getbutterfly.com/wordpress-plugins/" rel="external" target="_blank">getButterfly.com</a>.</p>
                        <p>&copy;<?php echo date('Y'); ?> <a href="https://getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <small>Code wrangling since 2005</small></p>
                    </div>
                </div>
            </div>

            <div id="poststuff">
                <div class="postbox">
                    <div class="inside">
                        <h3><?php _e('Modules', 'wp-perfect-plugin'); ?></h2>
                        <ul>
                            <li><a href="<?php echo admin_url('admin.php?page=w3p&tab=console'); ?>">Search Engine Console</a> - An almost complete solution for your search optimisation needs. Tracking codes, Open-Graph tags, local data and all the required SEO tweaks for your website.</li>
                        </ul>

                        <h3><?php _e('Shortcodes', 'wp-perfect-plugin'); ?></h2>
                        <ul>
                            <li><strong>List Subpages</strong> - Use the <code class="codor">[subpages]</code> shortcode to list the subpages of the current page as a <code class="codor">ul/li</code> list, allowing you to use parent pages in a similar way to categories. The <code class="codor">ul</code> structure is ready for styling using the <code class="codor">.w3p-subpages</code> CSS class.</li>
                        </ul>

                        <h3><?php _e('Functions', 'wp-perfect-plugin'); ?></h2>
                        <ul>
                            <li><strong>Microdata breadcrumbs</strong> - Use the <code class="codor">&lt;php if (function_exists('w3p_breadcrumbs')) { w3p_breadcrumbs(); } ?&gt;</code> template function to display breadcrumbs. Note that they are displayed as an <code class="codor">ol/li</code> list, and are unstyled.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        } else if ((string) $tab === 'settings') {
            if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                update_option('w3p_module_seo', (int) sanitize_text_field($_POST['w3p_module_seo']));
                update_option('w3p_module_mat', (int) sanitize_text_field($_POST['w3p_module_mat']));

                echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
            }
            ?>
            <form method="post" action="">
                <h3><?php _e('Module Settings', 'wp-perfect-plugin'); ?></h3>

                <p><span class="dashicons dashicons-editor-help"></span> This section allows you to configure your modules.</p>
                <p>
                    <input type="checkbox" id="w3p_module_seo" name="w3p_module_seo" value="1" disabled checked> <label for="w3p_module_seo">Enable search console module</label>
                </p>

                <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
            </form>
            <?php
        } else if ((string) $tab === 'console') {
            $subTab = isset($_GET['tab2']) ? (string) sanitize_text_field($_GET['tab2']) : 'verification'; ?>
    		<h2>Search Engine Console Settings</h2>

            <h3 class="nav-tab-wrapper">
                <a href="<?php echo admin_url('admin.php?page=w3p&tab=console&tab2=verification'); ?>" class="nav-tab <?php echo $subTab === 'verification' ? 'nav-tab-active' : ''; ?>"><?php _e('Verification and Relationships', 'wp-perfect-plugin'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=w3p&tab=console&tab2=homepage'); ?>" class="nav-tab <?php echo $subTab === 'homepage' ? 'nav-tab-active' : ''; ?>"><?php _e('Homepage', 'wp-perfect-plugin'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=w3p&tab=console&tab2=local'); ?>" class="nav-tab <?php echo $subTab === 'local' ? 'nav-tab-active' : ''; ?>"><?php _e('Local', 'wp-perfect-plugin'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=w3p&tab=console&tab2=analytics'); ?>" class="nav-tab <?php echo $subTab === 'analytics' ? 'nav-tab-active' : ''; ?>"><?php _e('Analytics', 'wp-perfect-plugin'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=w3p&tab=console&tab2=header_footer'); ?>" class="nav-tab <?php echo $subTab === 'header_footer' ? 'nav-tab-active' : ''; ?>"><?php _e('Header and Footer', 'wp-perfect-plugin'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=w3p&tab=console&tab2=opengraph'); ?>" class="nav-tab <?php echo $subTab === 'opengraph' ? 'nav-tab-active' : ''; ?>"><?php _e('Open Graph', 'wp-perfect-plugin'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=w3p&tab=console&tab2=misc'); ?>" class="nav-tab <?php echo $subTab === 'misc' ? 'nav-tab-active' : ''; ?>"><?php _e('Misc', 'wp-perfect-plugin'); ?></a>
            </h3>

            <?php
            if ((string) $subTab === 'verification') {
                if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                    update_option('w3p_google_webmaster', sanitize_text_field($_POST["w3p_google_webmaster"]));
                    update_option('w3p_bing_webmaster', sanitize_text_field($_POST["w3p_bing_webmaster"]));
                    update_option('w3p_yandex_webmaster', sanitize_text_field($_POST["w3p_yandex_webmaster"]));
                    update_option('w3p_pinterest_webmaster', sanitize_text_field($_POST["w3p_pinterest_webmaster"]));
                    update_option('w3p_wot_webmaster', sanitize_text_field($_POST["w3p_wot_webmaster"]));
                    update_option('w3p_majestic_webmaster', sanitize_text_field($_POST["w3p_majestic_webmaster"]));
                    update_option('w3p_baidu_webmaster', sanitize_text_field($_POST["w3p_baidu_webmaster"]));

                    update_option('w3p_twitter_author', sanitize_text_field($_POST["w3p_twitter_author"]));

                    echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
                }
                ?>
                <form method="post" action="">
                    <h3><?php _e('Search Engine Verification And Link Relationships', 'wp-perfect-plugin'); ?></h3>

                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to verify ownership of your site with Google Search Console, Bing Webmaster Tools, Yandex, Pinterest, Majestiv and Baidu and Web of Trust.</p>
                    <p>
                        <input name="w3p_google_webmaster" type="text" class="regular-text" value="<?php echo get_option('w3p_google_webmaster'); ?>"> <label>Google Search Console</label>
                        <br><small class="codor">&lt;meta name="google-site-verification" content="Volxdfasfasd3i3e_wATasfdsSDb0uFqvNVhLk7ZVY"&gt;</small>
                    </p>
                    <p>
                        <input name="w3p_bing_webmaster" type="text" class="regular-text" value="<?php echo get_option('w3p_bing_webmaster'); ?>"> <label>Bing Webmaster Tools</label>
                        <br><small class="codor">&lt;meta name="msvalidate.01" content="ASBKDW71D43Z67AB2D39636C89B88A"&gt;</small>
                    </p>
                    <p>
                        <input name="w3p_yandex_webmaster" type="text" class="regular-text" value="<?php echo get_option('w3p_yandex_webmaster'); ?>"> <label>Yandex Verification</label>
                        <br><small class="codor">&lt;meta name="yandex-verification" content="48b322931315c6df"&gt;</small>
                    </p>
                    <p>
                        <input name="w3p_pinterest_webmaster" type="text" class="regular-text" value="<?php echo get_option('w3p_pinterest_webmaster'); ?>"> <label>Pinterest Verification</label>
                        <br><small class="codor">&lt;meta name="p:domain_verify" content="3d392d258cd7fb8a5676ba12d06be0c6"&gt;</small>
                    </p>
                    <p>
                        <input name="w3p_wot_webmaster" type="text" class="regular-text" value="<?php echo get_option('w3p_wot_webmaster'); ?>"> <label>Web of Trust Verification</label>
                        <br><small class="codor">&lt;meta name="wot-verification" content="fb3819cb7126219ec5ca"&gt;</small>
                    </p>
                    <p>
                        <input name="w3p_majestic_webmaster" type="text" class="regular-text" value="<?php echo get_option('w3p_majestic_webmaster'); ?>"> <label>Majestic Verification</label>
                        <br><small class="codor">&lt;meta name="majestic-site-verification" content="MJ12_4995037f-d411-6872-1298-47523k450ag1"&gt;</small>
                    </p>
                    <p>
                        <input name="w3p_baidu_webmaster" type="text" class="regular-text" value="<?php echo get_option('w3p_baidu_webmaster'); ?>"> <label>Baidu Verification</label>
                        <br><small class="codor">&lt;meta name="baidu-site-verification" content="7V6m4wr5F2q2"&gt;</small>
                    </p>

                    <p>Missing a verification tag? Use the <a href="<?php echo admin_url('admin.php?page=w3p_search_console&tab=header_footer'); ?>">Header and Footer</a> section to add custom <code>meta</code> tags.</p>

                    <hr>
                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to specify link relationships with Twitter.</p>
                    <p>
                        @<input name="w3p_twitter_author" type="text" class="regular-text" value="<?php echo get_option('w3p_twitter_author'); ?>"> <label>Twitter Username</label>
                        <br><small>e.g. <span class="codor">getButterfly</span></small>
                    </p>

                    <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
                </form>
                <?php
            } else if ((string) $subTab === 'homepage') {
                if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                    update_option('w3p_homepage_description', stripslashes_deep(sanitize_text_field($_POST['w3p_homepage_description'])));

                    echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
                }
                ?>
                <form method="post" action="">
                    <h3>Homepage Details</h3>
                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to set a custom description for your homepage.</p>
                    <p>
                        <textarea name="w3p_homepage_description" class="large-text" rows="6"><?php echo get_option('w3p_homepage_description'); ?></textarea>
                    </p>
                    <p>Meta descriptions can be any length, but Google generally truncates snippets to ~155–160 characters. It's best to keep meta descriptions long enough that they're sufficiently descriptive, so we recommend descriptions between 50–160 characters. Keep in mind that the "optimal" length will vary depending on the situation, and your primary goal should be to provide value and drive clicks.</p>

                    <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
                </form>
                <?php
            } else if ((string) $subTab === 'local') {
                if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                    update_option('w3p_local', (int) $_POST['w3p_local']);
                    update_option('w3p_local_locality', sanitize_text_field($_POST['w3p_local_locality']));
                    update_option('w3p_local_region', sanitize_text_field($_POST['w3p_local_region']));
                    update_option('w3p_local_address', sanitize_text_field($_POST['w3p_local_address']));
                    update_option('w3p_local_postal_code', sanitize_text_field($_POST['w3p_local_postal_code']));
                    update_option('w3p_local_country', sanitize_text_field($_POST['w3p_local_country']));
                    update_option('w3p_telephone', sanitize_text_field($_POST['w3p_telephone']));

                    echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
                }
                ?>
                <form method="post" action="">
                    <h3>Local Business Details</h3>
                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to control your Knowledge Graph and various site schemas.</p>

                    <p>
                        <input name="w3p_local" id="w3p_local" type="checkbox" value="1" <?php if (get_option('w3p_local') == 1) echo 'checked'; ?>> <label for="w3p_local">Show Local Info</label>
                        <br><small>Only check this box if you have a Google Local address/business location!</small>
                    </p>
                    <p>
                        <input name="w3p_local_address" type="text" class="regular-text" value="<?php echo get_option('w3p_local_address'); ?>"> <label>Postal Address: Street Address</label>
                        <br><small>e.g. <span class="codor">1600 Amphitheatre Parkway</span></small>
                    </p>
                    <p>
                        <input name="w3p_local_locality" type="text" class="regular-text" value="<?php echo get_option('w3p_local_locality'); ?>"> <label>Postal Address: Locality</label>
                        <br><small>e.g. <span class="codor">Mountain View</span></small>
                    </p>
                    <p>
                        <input name="w3p_local_region" type="text" class="regular-text" value="<?php echo get_option('w3p_local_region'); ?>"> <label>Postal Address: Region</label>
                        <br><small>e.g. <span class="codor">CA</span></small>
                    </p>
                    <p>
                        <input name="w3p_local_postal_code" type="text" class="regular-text" value="<?php echo get_option('w3p_local_postal_code'); ?>"> <label>Postal Address: Postal Code</label>
                        <br><small>e.g. <span class="codor">94043</span></small>
                    </p>
                    <p>
                        <input name="w3p_local_country" type="text" class="regular-text" value="<?php echo get_option('w3p_local_country'); ?>"> <label>Postal Address: Country</label>
                        <br><small>e.g. <span class="codor">United States of America</span></small>
                    </p>
                    <p>
                        <input name="w3p_telephone" type="text" class="regular-text" value="<?php echo get_option('w3p_telephone'); ?>"> <label>Telephone</label>
                        <br><small>e.g. <span class="codor">+1 650-253-0000</span></small>
                    </p>

                    <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
                </form>
                <?php
            } else if ((string) $subTab === 'analytics') {
                if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                    update_option('w3p_google_analytics', sanitize_text_field($_POST['w3p_google_analytics']));
                    update_option('w3p_google_tag_manager', sanitize_text_field($_POST['w3p_google_tag_manager']));

                    echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
                }
                ?>
                <form method="post" action="">
                    <h3>Analytics and Tag Management</h3>
                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to set Google Analytics and/or Google Tag Manager.</p>
                    <p>
                        <input name="w3p_google_analytics" type="text" class="regular-text" value="<?php echo get_option('w3p_google_analytics'); ?>"> <label>Google Analytics</label>
                        <br><small>(Web Property ID: <span class="codor">UA-XXXXXXX-X</span>)</small>
                    </p>
                    <p>
                        <input name="w3p_google_tag_manager" type="text" class="regular-text" value="<?php echo get_option('w3p_google_tag_manager'); ?>"> <label>Google Tag Manager</label>
                        <br><small>(Container ID: <span class="codor">GTM-XXXX</span>)</small>
                    </p>

                    <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
                </form>
                <?php
            } else if ((string) $subTab === 'header_footer') {
                if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                    update_option('w3p_head_section', stripslashes_deep(sanitize_text_field($_POST['w3p_head_section'])));
                    update_option('w3p_footer_section', stripslashes_deep(sanitize_text_field($_POST['w3p_footer_section'])));

                    echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
                }
                ?>
                <form method="post" action="">
                    <h3>Header/Footer Management</h3>
                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to add custom HTML code to header (<small class="codor">&lt;head&gt;</small>) and footer areas.</p>
                    <p>
                        Add code to the header of your site<br>
                        <textarea name="w3p_head_section" class="large-text code" rows="6"><?php echo get_option('w3p_head_section'); ?></textarea>
                    </p>
                    <p>
                        Add code to the footer of your site<br>
                        <textarea name="w3p_footer_section" class="large-text code" rows="6"><?php echo get_option('w3p_footer_section'); ?></textarea>
                    </p>

                    <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
                </form>
                <?php
            } else if ((string) $subTab === 'opengraph') {
                if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                    update_option('w3p_og', (int) $_POST['w3p_og']);
                    update_option('w3p_fb_default_image', esc_url($_POST['w3p_fb_default_image']));

                    update_option('w3p_fb_admin_id', (int) $_POST["w3p_fb_admin_id"]);
                    update_option('w3p_fb_app_id', (int) $_POST["w3p_fb_app_id"]);

                    echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
                }
                ?>
                <form method="post" action="">
                    <h3>Open Graph</h3>
                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to enable/disable automatic Open Graph tags. Open Graph data is used primarily for Facebook, Twitter and Pinterest.</p>
                    <p>
                        <input name="w3p_og" id="w3p_og" type="checkbox" value="1" <?php if (get_option('w3p_og') == 1) echo 'checked'; ?>> <label for="w3p_og">Add Open Graph data</label>
                        <br><small>This option requires your Facebook Admin ID and Application ID.</small>
                    </p>
                    <p><span class="dashicons dashicons-lightbulb"></span> Debug your Open Graph details by using <a href="https://developers.facebook.com/tools/debug/" rel="external" target="_blank">Facebook Sharing Debugger</a> tool.</p>

                    <hr>
                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to specify Facebook details.</p>
                    <p>
                        <input name="w3p_fb_admin_id" type="text" class="regular-text" value="<?php echo get_option('w3p_fb_admin_id'); ?>"> <label>Facebook Admin ID</label>
                    </p>
                    <p>
                        <input name="w3p_fb_app_id" type="text" class="regular-text" value="<?php echo get_option('w3p_fb_app_id'); ?>"> <label>Facebook Application ID</label>
                    </p>
                    <p>
                        <input name="w3p_fb_default_image" id="w3p_fb_default_image" type="url" class="regular-text" value="<?php echo get_option('w3p_fb_default_image'); ?>" placeholder="https://"> <label for="w3p_fb_default_image">Default Open Graph image</label>
                    </p>

                    <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
                </form>
                <?php
            } else if ((string) $subTab === 'misc') {
                if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                    update_option('w3p_od', (int) $_POST['w3p_od']);

                    echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
                }
                ?>
                <form method="post" action="">
                    <h3>Miscellaneous</h3>
                    <p><span class="dashicons dashicons-editor-help"></span> This section allows you to set various SEO/SEM options.</p>
                    <p>
                        <input name="w3p_od" id="w3p_od" type="checkbox" value="1" <?php if (get_option('w3p_od') == 1) echo 'checked'; ?>> <label for="w3p_od">Enable optimised descriptions</label>
                        <br><small>Automatically generate description content for posts, pages, categories, tags and homepage. Note that <b>pages now support excerpts</b>.</small>
                    </p>

                    <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
                </form>
                <?php
            }
        } else if ((string) $tab === '') {
            if (isset($_POST['info_update1']) && current_user_can('manage_options')) {
                echo '<div class="updated notice is-dismissible"><p>Settings updated!</p></div>';
            }
            ?>
            <form method="post" action="">
                <h3><?php _e('More Settings', 'wp-perfect-plugin'); ?></h3>

                <p><input type="submit" name="info_update1" class="button button-primary" value="<?php _e('Save Changes', 'wp-perfect-plugin'); ?>"></p>
            </form>
            <?php
        }
        ?>
	</div>
<?php
}
