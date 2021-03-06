<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function w3p_search_console_head() {
	$w3pGoogleMeta = get_option('w3p_google_webmaster');
	$w3pBingMeta = get_option('w3p_bing_webmaster');
	$w3pYandexMeta = get_option('w3p_yandex_webmaster');
	$w3pPinterestMeta = get_option('w3p_pinterest_webmaster');
	$w3pWoTMeta = get_option('w3p_wot_webmaster');
	$w3pMajesticMeta = get_option('w3p_majestic_webmaster');
	$w3pBaiduMeta = get_option('w3p_baidu_webmaster');

    $twitter_author_rel = get_option('w3p_twitter_author');

    $google_an = get_option('w3p_google_analytics');
    $google_tm = get_option('w3p_google_tag_manager');

	$head_section = get_option('w3p_head_section');

    /**
     * Add custom head content
     */
	if (!empty($head_section)) {
		echo $head_section;
	}

    /**
     * Add search engine meta verification tags
     */
	if (!empty($w3pGoogleMeta)) {
		echo '<meta name="google-site-verification" content="' . $w3pGoogleMeta . '">';
	}
	if (!empty($w3pBingMeta)) {
		echo '<meta name="msvalidate.01" content="' . $w3pBingMeta . '">';
	}
	if (!empty($w3pYandexMeta)) {
		echo '<meta name="yandex-verification" content="' . $w3pYandexMeta . '">';
	}
	if (!empty($w3pPinterestMeta)) {
		echo '<meta name="p:domain_verify" content="' . $w3pPinterestMeta . '">';
	}
	if (!empty($w3pWoTMeta)) {
		echo '<meta name="wot-verification" content="' . $w3pWoTMeta . '">';
	}
	if (!empty($w3pMajesticMeta)) {
		echo '<meta name="majestic-site-verification" content="' . $w3pMajesticMeta . '">';
	}
	if (!empty($w3pBaiduMeta)) {
		echo '<meta name="baidu-site-verification" content="' . $w3pBaiduMeta . '">';
	}

    /**
     * Add custom social relationship
     */
    if (!empty($twitter_author_rel)) {
		echo '<link rel="me" href="https://twitter.com/' . $twitter_author_rel . '">';
	}

    /**
     * Add Google Analytics
     */
    if (!empty($google_an)) {
        echo '<!-- Global site tag (gtag.js) - Google Analytics - WP Perfect Plugin -->
<script async src="https://www.googletagmanager.com/gtag/js?id=' . $google_an . '"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("js", new Date());
gtag("config", "' . $google_an . '");
</script>' . "\n";
	}

    /**
     * Add Google Tag Manager
     */
    if (!empty($google_tm)) {
        echo '<!-- Google Tag Manager - WP Perfect Plugin -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
"https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,"script","dataLayer","' . $google_tm . '");</script>
<!-- End Google Tag Manager -->' . "\n";
	}
}

function w3p_search_console_footer() {
    $out = '';
	$footer_section = get_option('w3p_footer_section');

    $w3p_local = get_option('w3p_local');
    $w3p_local_locality = get_option('w3p_local_locality');
    $w3p_local_region = get_option('w3p_local_region');
    $w3p_local_address = get_option('w3p_local_address');
    $w3p_local_postal_code = get_option('w3p_local_postal_code');
    $w3p_local_country = get_option('w3p_local_country');
    $w3p_telephone = get_option('w3p_telephone');

    $twitter_author_rel = get_option('w3p_twitter_author');

    if ((int) $w3p_local === 1) {
        $out .= '<!-- W3P Local -->
        <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Organization",
            "name": "' . get_bloginfo('name') . '",
            "url" : "' . get_bloginfo('url') . '",
            "description": "' . get_bloginfo('description') . '",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "' . $w3p_local_address . '",
                "addressLocality": "' . $w3p_local_locality . '",
                "addressRegion": "' . $w3p_local_region . '",
                "postalCode": "' . $w3p_local_postal_code . '",
                "addressCountry": "' . $w3p_local_country . '"
            },
            "telephone": "' . $w3p_telephone . '"
        }
        </script>
        <script type="application/ld+json">
        {
            "@context" : "http://schema.org",
            "@type" : "LocalBusiness",
            "name" : "' . get_bloginfo('name') . '",
            "url" : "' . get_bloginfo('url') . '",
            "sameAs" : [
                "' . $twitter_author_rel . '"
            ]
        }
        </script>';
    }

    if (!empty($footer_section)) {
		$out .= $footer_section;
	}

    echo $out;
}



// Add Open Graph language attributes
function w3p_opengraph_doctype($output) {
    return $output . ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"';
}

if ((int) get_option('w3p_og') === 1) {
    add_action('wp_head', 'w3p_head_og');
    add_filter('language_attributes', 'w3p_opengraph_doctype');
}

// Remove HTML comments
function w3p_remove_html_comments($content = '') {
	return preg_replace('/<!--(.|\s)*?-->/', '', $content);
}

// Add Open Graph meta info
function w3p_head_og() {
    global $post;

    if (empty($post->ID)) {
        return;
    }

    $w3pExcerpt = get_the_excerpt($post->ID);
    $w3pExcerpt = strip_shortcodes($w3pExcerpt);
    $w3pExcerpt = wp_strip_all_tags($w3pExcerpt);

    if (empty($w3pExcerpt)) {
        $w3pExcerpt = w3p_remove_html_comments(get_the_content('', '', $post->ID));
        $w3pExcerpt = strip_shortcodes($w3pExcerpt);
        $w3pExcerpt = wp_strip_all_tags($w3pExcerpt);
        $w3pExcerpt = substr($w3pExcerpt, 0, 300);
    }
    if (is_category()) {
        $w3pExcerpt = wp_strip_all_tags(category_description());
    }

    $mt = '';
    $og = '';
    $fb = '';
    $tw = '';

    $mt .= '<meta name="description" content="' . $w3pExcerpt . '">';

    if (is_front_page()) {
        $og .= '<meta property="og:type" content="website">';
    } else {
        $og .= '<meta property="og:type" content="article">';
    }

    // 10
    $og .= '<meta property="og:locale" content="' . get_locale() . '">
    <meta property="og:url" content="' . get_permalink() . '">
    <meta property="og:site_name" content="' . get_bloginfo('name') . '">
    <meta property="og:title" content="' . strip_tags(get_the_title()) . '">
    <meta property="og:description" content="' . $w3pExcerpt . '">';

    // Twitter
    $tw .= '<meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@' . get_option('w3p_twitter_author') . '">
    <meta name="twitter:creator" content="@' . get_option('w3p_twitter_author') . '">
    <meta name="twitter:title" content="' . strip_tags(get_the_title()) . '">
    <meta name="twitter:description" content="' . $w3pExcerpt . '">';

    // Facebook
    $fb .= '<meta property="fb:app_id" content="' . get_option('w3p_fb_app_id') . '">';

    if (!has_post_thumbnail($post->ID)) {
        if (!empty(get_option('w3p_fb_default_image'))) {
            $og .= '<meta property="og:image" content="' . get_option('w3p_fb_default_image') . '">';
        }
    } else {
        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        $thumbnail_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);

        $og .= '<meta property="og:image" content="' . esc_attr($thumbnail_src[0]) . '">
        <meta property="og:image:secure_url" content="' . esc_attr($thumbnail_src[0]) . '">
        <meta property="og:image:width" content="' . $thumbnail_src[1] . '">
        <meta property="og:image:height" content="' . $thumbnail_src[2] . '">
        <meta property="og:image:alt" content="' . $thumbnail_alt . '">';

        $tw .= '<meta name="twitter:image" content="' . esc_attr($thumbnail_src[0]) . '">';
    }

    echo $mt . $og . $fb . $tw;
}

function w3p_add_excerpts_to_pages() {
    add_post_type_support('page', 'excerpt');
}



add_action('wp_head', 'w3p_search_console_head');
add_action('wp_footer', 'w3p_search_console_footer');



/*
 * WordPress Microdata breadcrumbs
 *
 * #reference https://developers.google.com/search/docs/data-types/breadcrumbs
 */
function w3p_breadcrumb_wrapper($title, $link, $class, $counter) {
    if ($link !== '#') {
        $item = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="' . $class . '"><a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="' . $link . '"><span itemprop="name">' . $title . '</span></a><meta itemprop="position" content="' . $counter . '"></li>';
    } else {
        $item = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="' . $class . '"><span itemprop="name">' . $title . '</span><meta itemprop="position" content="' . $counter . '"></li>';
    }

    return $item;
}

function w3p_breadcrumbs() {
    global $post, $wp_query;

    // Settings
    $counter = 1;

    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy = 'product_cat';

    // Do not display on the homepage
    if (!is_front_page()) {
        echo '<ol itemscope itemtype="http://schema.org/BreadcrumbList" class="w3p-breadcrumbs">';

        // Home page
        echo w3p_breadcrumb_wrapper('Home', get_home_url(), 'item-home', $counter);
        $counter++;

        if (is_archive() && !is_tax() && !is_category() && !is_tag()) {
            echo w3p_breadcrumb_wrapper(post_type_archive_title($prefix, false), '#', 'item-current item-archive', $counter);
            $counter++;
        } else if (is_archive() && is_tax() && !is_category() && !is_tag()) {
            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if ($post_type !== 'post') {
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);

                echo w3p_breadcrumb_wrapper($post_type_object->labels->name, $post_type_archive, 'item-cat item-custom-post-type-' . $post_type . '', $counter);
                $counter++;
            }

            $custom_tax_name = get_queried_object()->name;
            echo w3p_breadcrumb_wrapper($custom_tax_name, '#', 'item-current item-archive', $counter);
            $counter++;
        } else if (is_single()) {
            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if ($post_type !== 'post') {
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);

                echo w3p_breadcrumb_wrapper($post_type_object->labels->name, $post_type_archive, 'item-cat item-custom-post-type-' . $post_type . '', $counter);
                $counter++;
            }

            // Get post category info
            $category = get_the_category();

            if (!empty($category)) {
                // Get last category post is in
                $last_category = end(array_values($category));

                // Get parent any categories and create array
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','), ',');
                $cat_parents = explode(',', $get_cat_parents);

                // Loop through parent categories and store in variable $cat_display
                $cat_display = '';
                foreach ($cat_parents as $parents) {
                    $cat_display .= w3p_breadcrumb_wrapper($parents, '#', 'item-cat', $counter);
                    $counter++;
                }
            }

            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if (empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                $taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
                $cat_id = $taxonomy_terms[0]->term_id;
                $cat_nicename = $taxonomy_terms[0]->slug;
                $cat_link = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name = $taxonomy_terms[0]->name;
            }

            // Check if the post is in a category
            if (!empty($last_category)) {
                echo $cat_display;
                echo w3p_breadcrumb_wrapper(get_the_title(), '#', 'item-current item-' . $post->ID . '', $counter);
                $counter++;

                // Else if post is in a custom taxonomy
            } else if (!empty($cat_id)) {
                echo w3p_breadcrumb_wrapper($cat_name, $cat_link, 'item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '', $counter);
                $counter++;

                echo w3p_breadcrumb_wrapper(get_the_title(), '#', 'item-current item-' . $post->ID . '', $counter);
                $counter++;
            } else {
                echo w3p_breadcrumb_wrapper(get_the_title(), '#', 'item-current item-' . $post->ID . '', $counter);
                $counter++;
            }
        } else if (is_category()) {
            // Category page
            echo w3p_breadcrumb_wrapper(single_cat_title('', false), '#', 'item-current item-cat', $counter);
            $counter++;
        } else if (is_page()) {
            // Standard page
            if ($post->post_parent) {
                // If child page, get parents
                $anc = get_post_ancestors($post->ID);

                // Get parents in the right order
                $anc = array_reverse($anc);

                // Parent page loop
                if (!isset($parents)) {
                    $parents = null;
                }

                foreach ($anc as $ancestor) {
                    $parents .= w3p_breadcrumb_wrapper(get_the_title($ancestor), get_permalink($ancestor), 'item-parent item-parent-' . $ancestor . '', $counter);
                    $counter++;
                }

                // Display parent pages
                echo $parents;

                // Current page
                echo w3p_breadcrumb_wrapper(get_the_title(), '#', 'item-current item-' . $post->ID . '', $counter);
                $counter++;
            } else {
                // Just display current page if not parents
                echo w3p_breadcrumb_wrapper(get_the_title(), '#', 'item-current item-' . $post->ID . '', $counter);
                $counter++;
            }
        } else if (is_tag()) {
            // Tag page

            // Get tag information
            $term_id = get_query_var('tag_id');
            $taxonomy = 'post_tag';
            $args = 'include=' . $term_id;
            $terms = get_terms($taxonomy, $args);
            $get_term_id = $terms[0]->term_id;
            $get_term_slug = $terms[0]->slug;
            $get_term_name = $terms[0]->name;

            // Display the tag name
            echo w3p_breadcrumb_wrapper($get_term_name, '#', 'item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '', $counter);
            $counter++;
        } else if (is_day()) {
            // Day archive

            // Year link
            echo w3p_breadcrumb_wrapper(get_the_time('Y'), get_year_link(get_the_time('Y')), 'item-year item-year-' . get_the_time('Y') . '', $counter);
            $counter++;

            // Month link
            echo w3p_breadcrumb_wrapper(get_the_time('M'), get_month_link(get_the_time('Y'), get_the_time('m')), 'item-month item-month-' . get_the_time('m') . '', $counter);
            $counter++;

            // Day display
            echo w3p_breadcrumb_wrapper(get_the_time('jS') . ' ' . get_the_time('M'), '#', 'item-current item-' . get_the_time('j') . '', $counter);
            $counter++;
        } else if (is_month()) {
            // Month Archive

            // Year link
            echo w3p_breadcrumb_wrapper(get_the_time('Y'), '#', 'item-current item-year item-year-' . get_the_time('Y') . '', $counter);
            $counter++;

            // Month display
            echo w3p_breadcrumb_wrapper(get_the_time('M'), '#', 'item-current item-month item-month-' . get_the_time('m') . '', $counter);
            $counter++;
        } else if (is_year()) {
            // Display year archive
            echo w3p_breadcrumb_wrapper(get_the_time('Y'), '#', 'item-current item-current-' . get_the_time('Y') . '', $counter);
            $counter++;
        } else if (is_author()) {
            // Author archive

            // Get the author information
            global $author;
            $userdata = get_userdata($author);

            // Display author name
            echo w3p_breadcrumb_wrapper($userdata->display_name, '#', 'item-current item-current-' . $userdata->user_nicename . '', $counter);
            $counter++;
        } else if (get_query_var('paged')) {
            // Paginated archives
            echo w3p_breadcrumb_wrapper(get_query_var('paged'), '#', 'item-current item-current-' . get_query_var('paged') . '', $counter);
            $counter++;
        } else if (is_search()) {
            // Search results page
            echo w3p_breadcrumb_wrapper(get_search_query(), '#', 'item-current item-current-' . get_search_query() . '', $counter);
            $counter++;
        } else if (is_404()) {
            // 404 page
            echo w3p_breadcrumb_wrapper('404', '#', '', $counter);
            $counter++;
        }
        echo '</ol>';
    }
}
