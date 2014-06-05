<?php
/**
 * @version   2.0.1 May 28, 2014
 * @author    JaidynReiman http://www.jrconway.net
 * @copyright Copyright (C) 2014 JaidynReiman
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


#######################################
## -- START GANTRY FRAMEWORK
#######################################

/**
 * Check to see if Gantry is Active
 * 
 * @return bool
 */
function gantry_theme_is_gantry_active()
{
    $active = false;
    $active_plugins = get_option( 'active_plugins' );
    if ( in_array( 'gantry/gantry.php', $active_plugins ) ) {
        $active = true;
    }
    if ( !function_exists( 'is_plugin_active_for_network' ) )
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    if ( is_plugin_active_for_network( 'gantry/gantry.php' ) ) {
        $active = true;
    }
    return $active;
}

/**
 * @return void
 */
function gantry_admin_missing_nag()
{
    $msg = __( 'The active theme requires the Gantry Framework Plugin to be installed and active' );
    echo "<div class='update-nag'>$msg</div>";
}

/**
 * @return void
 */
function gantry_missing_nag()
{
    echo 'This theme requires the Gantry Framework Plugin to be installed and active.';
    die(0);
}


if ( !gantry_theme_is_gantry_active() ) {
    if ( !is_admin() ) {
        add_filter( 'template_include', 'gantry_missing_nag', -10, 0 );
    }
    else {
        add_action( 'admin_notices', 'gantry_admin_missing_nag' );
    }
}

// This will always set the Posts Per Page option to 1 to fix the WordPress bug
// when the pagination would return 404 page. To set the number of posts shown
// on the blog page please use the field under Theme Settings > Content > Blog > Post Count
function gantry_posts_per_page() {
    if( get_option( 'posts_per_page' ) != '1' ) update_option( 'posts_per_page', '1' );
}

add_action( 'init', 'gantry_posts_per_page' );

/**
 * Function to generate post pagination
 */
function gantry_pagination($custom_query) {
    global $gantry;

    if ( !$current_page = get_query_var( 'paged' ) ) $current_page = 1;
            
    $permalinks = get_option( 'permalink_structure' );
    if( is_front_page() ) {
        $format = empty( $permalinks ) ? '?paged=%#%' : 'page/%#%/';
    } else {
        $format = empty( $permalinks ) || is_search() ? '&paged=%#%' : 'page/%#%/';
    }

    $big = 999999999; // need an unlikely integer

    $pagination = paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => $format,
        'current' => $current_page,
        'total' => $custom_query->max_num_pages,
        'mid_size' => $gantry->get( 'pagination-count', '8' ),
        'type' => 'list',
        'next_text' => _r( 'Next' ),
        'prev_text' => _r( 'Previous' )
    ) );

    $pagination = explode( "\n", $pagination );
    $pagination_mod = array();

    foreach ( $pagination as $item ) {
        ( preg_match( '/<ul class=\'page-numbers\'>/i', $item ) ) ? $item = str_replace( '<ul class=\'page-numbers\'>', '<ul>', $item ) : $item;
        ( preg_match( '/class="prev/i', $item ) ) ? $item = str_replace( '<li', '<li class="pagination-prev"', $item ) : $item;
        ( preg_match( '/class="next/i', $item ) ) ? $item = str_replace( '<li', '<li class="pagination-next"', $item ) : $item;
        ( preg_match( '/page-numbers/i', $item ) ) ? $item = str_replace( 'page-numbers', 'page-numbers pagenav', $item ) : $item;
        $pagination_mod[] .= $item;
    }
    
    ?>
    
    <div class="pagination">

        <?php if( $gantry->get( 'pagination-show-results', '1' ) ) : ?>
        <p class="counter">
            <?php printf( _r( 'Page %1$s of %2$s' ), $current_page, $custom_query->max_num_pages ); ?>
        </p>
        <?php endif; ?>
    
        <?php foreach( $pagination_mod as $page ) {
            echo $page;
        } ?>

    </div>

<?php }



#######################################
## -- START CUSTOMIZATION OPTIONS
#######################################


if ( ! function_exists( 'jrblog_custom_styles' ) ) :
/**
 * Set Custom Stylesheets
 *
 * @since jrBlog 1.9.3
 */
function jrblog_custom_styles() {
	/*
	 * Loads our main stylesheet.
	 */
	wp_enqueue_style( 'jrblog-styles', get_template_directory_uri() . '/css/style.less');

	/*
	 * Load jQuery
	 */
	if (!is_admin()) {
		wp_enqueue_script('jquery');
	}

	/*
	 * Load Javascript Functions
	 */
	wp_enqueue_script( 'modernizr', dirname(get_template_directory_uri()) . '/js/libs/modernizr-2.0.6.min.js');
	wp_enqueue_script( 'twitter-bootstrap', dirname(get_template_directory_uri()) . '/js/bootstrap.min.js', array('jquery'));
	wp_enqueue_script( 'jrblog-scripts', dirname(get_template_directory_uri()) . '/js/main.js', array('jquery'));

	/*
	 * CHILD THEME:
	 *
	 * Copy this function to the functions.php file in the new child theme.
	 * Copy css/child.less to your child themes css folder and rename it style.less.
	 * Change "child" in the line below to your child theme's directory name.
	 * Uncomment the line below.
	 */
	//wp_enqueue_style( 'jrblog-styles', dirname(get_template_directory_uri()) . '/child/css/style.less');
}
endif;
add_action( 'wp_enqueue_scripts', 'jrblog_custom_styles' );

if ( ! function_exists( 'jrblog_extend_contact' ) ) :
/**
 * Extend Custom Contact Fields
 *
 * @since jrBlog 1.0
 */
function jrblog_extend_contact($fields) {
	/**
	  * CHILD THEME:
	  *
	  * Copy this function to the functions.php of your child theme and 
	  * perform contact field overrides in the same way as the default.
	  *
	  * You can also override the default function, but this is NOT
	  * AT ALL recommended.
	  */

	// Add Array Overrides

	// EXAMPLE: DISABLE FIELD
	/*$fields['skype'] = false;*/

	// EXAMPLE: ADD NEW FIELD
	/*$fields['myspace] = array(
		"name"   => "MySpace",
		"url"    => "http://new.myspace.com/",
		"share"  => false
	);*/

	// Return Contact Fields
	return $fields;
}
endif;

if ( ! function_exists( 'jrblog_custom_contact' ) ) :
/**
 * Set Custom Contact Fields
 *
 * @since jrBlog 1.0
 */
function jrblog_custom_contact() {
	/**
	  * Format in the following way:
	  *
	  * "type" => array(
	  *     "name"   => "Type",    // Full name of contact type
	  *     "url"    => "http://", // URL location to personal profile
	  *     "url"    => "",        // Don't set to disable follow options in theme options
	  *     "share"  => true,      // Enable sharing options in theme options
	  *     "share"  => false,     // Disable sharing options in theme options
	  * ),
	  *
	  *
	  * To disable existing fields:
	  *
	  * "type" => false,           // Set full array to "false" to remove it entirely.
	  */

	// Return Array of Contact Fields
	$fields = array(
		"aim"              => false,
		"yim"              => false,
		"jabber"           => false,
		"skype"            => array(
			"name"   => "Skype",
			"url"    => "",
			"share"  => false
		),
		"facebook"         => array(
			"name"   => "Facebook",
			"url"    => "http://www.facebook.com/",
			"share"  => true
		),
		"twitter"          => array(
			"name"   => "Twitter",
			"url"    => "http://www.twitter.com/",
			"share"  => true
		),
		"googleplus"       => array(
			"name"   => "Google+",
			"url"    => "http://plus.google.com/",
			"share"  => true
		),
		"linkedin"         => array(
			"name"   => "LinkedIn",
			"url"    => "http://www.linkedin.com/pub/",
			"share"  => true
		),
		"tumblr"           => array(
			"name"   => "Tumblr",
			"url"    => "http://www.tumblr.com",
			"share"  => false,
			"sub"    => true
		)
	);

	// Extend Custom Fields
	$fields = jrblog_extend_contact($fields);

	// Return Contact Fields
	return $fields;
}
endif;

#######################################
## -- END CUSTOMIZATION OPTIONS
#######################################



#######################################
## -- START INITIALIZATION FUNCTIONS
#######################################

/**
 * Sets up theme defaults and registers the various WordPress features that
 * jrBlog supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since jrBlog 1.0
 */
function jrblog_setup() {
	/*
	 * Makes Template available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 */
	load_theme_textdomain( 'jrblog', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop
}
add_action( 'after_setup_theme', 'jrblog_setup' );

if ( ! function_exists( 'jrblog_schema' ) ):
/**
 * Gets Site Schema From Theme Options
 *
 * @since jrBlog 1.0
 */
function jrblog_schema() {
	// Get Theme Options
	//$theme_options = jrblog_get_theme_options();
	$theme_options = array();

	// Output Schema
	if(!empty($theme_options['schema'])) {
		echo $theme_options['schema'];
	}
	else {
		echo "Blog";
	}
}
endif; // jrblog_schema

/**
 * Add Extra Contact Fields
 *
 * @since jrBlog 1.0
 */
function jrblog_contact_info($contactmethods) {
	// Get Social Sites
	$fields = jrblog_custom_contact();

	// Loop Social Sites
	foreach($fields as $code => $field) {
		// Valid Site?
		if(!empty($field) && is_array($field) && count($field)) {
			// Add to Contact Methods
			$contactmethods[$code] = $field['name'];
		}
		elseif(empty($field['name'])) {
			// Delete from Contact Methods
			unset($contactmethods[$code]);
		}
	}

	// Return Contact Methods
	return $contactmethods;
}
add_filter('user_contactmethods', 'jrblog_contact_info');

#######################################
## -- END INITIALIZATION FUNCTIONS
#######################################


#######################################
## -- START SOCIAL FUNCTIONS
#######################################

/**
 * Create Social Share Buttons
 *
 * @author David A Conway Jr.
 * @param string $url  : the url to share; defaults to current page's url
 * @param string $text : the text in the share popup; defaults to current page's title
 * @since jrBlog 1.0
 */
function jrblog_share_buttons($url = '', $text = '') {
    return false;
	// All Sharing Disabled?
	if(of_get_option('share_disable')) {
		return '';
	}

	// Get Social Sites
	$fields = jrblog_custom_contact();
	$html   = '';

	// Loop Social Sites
	foreach($fields as $code => $field) {
		// Valid Site?
		if(!empty($field) && is_array($field) && count($field)) {
			// Set Unique Variables
			$share = of_get_option($code . '_share');
			$func  = 'jrblog_' . $code . '_button';

			// Add to Contact Methods
			if(!empty($share) && function_exists($func)) {
				// Get Share Button
				$html .= $func($url, $text);
			}
		}
	}

	// Return Share HTML
	return $html;
}

/**
 * Create Facebook Share Button
 *
 * @author David A Conway Jr.
 * @param string $url  : the url to share; defaults to current page's url
 * @param string $text : the text in the share popup; defaults to current page's title
 * @since jrBlog 1.0
 */
function jrblog_facebook_button($url = '', $text = '') {
	// Include HREF?
	$href = '';
	if(!empty($url)) {
		$href = ' data-href="' . $url . '"';
	}

	// Include Text?
	$cont = '';
	if(!empty($text)) {
		$cont = ' data-text="' . $text . '"';
	}

	// Generate HTML
	$html = '<div class="fb-like"' . $href . $prev . ' data-send="false" data-layout="button_count" data-width="80" data-show-faces="false"></div>';

	// Return HTML Code
	return $html;
}

/**
 * Create Twitter Share Button
 *
 * @author David A Conway Jr.
 * @param string $url  : the url to share; defaults to current page's url
 * @param string $text : the text in the share popup; defaults to current page's title
 * @since jrBlog 1.0
 */
function jrblog_twitter_button($url = '', $text = '') {
	// Include HREF?
	$href = '';
	if(!empty($url)) {
		$href = ' data-url="' . $url . '"';
	}

	// Include Text?
	$cont = '';
	if(!empty($text)) {
		$cont = ' data-text="' . $text . '"';
	}

	// Generate HTML
	$html = '<a href="https://twitter.com/share" class="twitter-share-button"' . $href . $cont . '>Tweet</a>';

	// Return HTML Code
	return $html;
}

/**
 * Create Google+ Share Button
 *
 * @author David A Conway Jr.
 * @param string $url  : the url to share; defaults to current page's url
 * @param string $text : the text in the share popup; defaults to current page's title
 * @since jrBlog 1.0
 */
function jrblog_googleplus_button($url = '', $text = '') {
	// Include HREF?
	$href = '';
	if(!empty($url)) {
		$href = ' data-href="' . $url . '"';
	}

	// Include Text?
	$cont = '';
	if(!empty($text)) {
		$cont = ' data-text="' . $text . '"';
	}

	// Generate HTML
	$html = '<div class="g-plus" data-action="share" data-annotation="bubble"' . $href . '></div>';

	// Return HTML Code
	return $html;
}

/**
 * Create LinkedIn Share Button
 *
 * @author David A Conway Jr.
 * @param string $url  : the url to share; defaults to current page's url
 * @param string $text : the text in the share popup; defaults to current page's title
 * @since jrBlog 1.0
 */
function jrblog_linkedin_button($url = '', $text = '') {
	// Include HREF?
	$href = '';
	if(!empty($url)) {
		$href = ' data-url="' . $url . '"';
	}

	// Include Text?
	$cont = '';
	if(!empty($text)) {
		$cont = ' data-text="' . $text . '"';
	}

	// Generate HTML
	$html = '<script type="IN/Share"' . $href . ' data-counter="right"></script>';

	// Return HTML Code
	return $html;
}


/**
 * Create Social Follow Icons
 *
 * @author David A Conway Jr.
 * @since jrBlog 1.0
 */
function jrblog_follow_icons($force = false) {
    return false;
	// All Links Disabled?
	if(of_get_option('follow_disable') && empty($force)) {
		return '';
	}


	// RSS Follow Enabled?
	$html    = '';
	$code    = 'rss';
	$size    = of_get_option('follow_dims');
	$follow  = of_get_option($code . '_follow');
	if(!empty($follow)) {
		// Set RSS Variables
		$img     = '';
		$rss     = '/feed';
		$url     = 'http://feeds.feedburner.com/';
		$acct    = of_get_option($code . '_acct');
		$icon    = of_get_option($code . '_icon');
		$custom  = of_get_option($code . '_custom');
		$default = '/images/icons/' . $size . '/' . $code . '.png';

		// Feedburner Enabled?
		if(!empty($acct)) {
			// Full URL Provided?
			if(strpos($acct, 'http') !== false) {
				$feed = $acct;
			}
			// Set Feedburner Feed
			else {
				$feed = $url . $acct;
			}
		}
		// Use Internal Feed
		else {
			// Set WP Feed
			$feed = $rss;
		}

		// Use Custom Button?
		if(!empty($custom)) {
			// Set Custom Icon
			$img = $icon;
		}
		// Use Default Button
		else {
			// Set Default Icon
			$img = get_template_directory_uri() . $default;
		}

		// Add Link HTML
		$html .= '<a href="' . $url . $acct . '" target="_blank"><img src="' . $img . '" alt="" width="' . $size . '" height="' . $size . '" /></a>';
	}


	// Get Social Sites
	$fields = jrblog_custom_contact();

	// Loop Social Sites
	foreach($fields as $code => $field) {
		// Valid Site?
		if(!empty($field) && is_array($field) && count($field)) {
			// Set Unique Variables
			$img     = '';
			$href    = '';
			$sub     = $field['sub'];
			$url     = $field['url'];
			$acct    = of_get_option($code . '_acct');
			$icon    = of_get_option($code . '_icon');
			$custom  = of_get_option($code . '_custom');
			$follow  = of_get_option($code . '_follow');
			$default = '/images/icons/' . $size . '/' . $code . '.png';

			// Full URL Provided?
			if(strpos($acct, 'http') !== false) {
				// Set Specific Domain
				$href = $acct;
			}
			// Is a Subdomain?
			elseif(!empty($sub)) {
				// Replace Subdomain
				$href = str_replace('www', $acct, $url);
			}
			// Set Social URL
			else {
				// Set Default Domain
				$href = $url . $acct;
			}

			// Add to Contact Methods
			if(!empty($follow) && !empty($url) && !empty($acct)) {
				// Use Custom Button?
				if(!empty($custom)) {
					// Set Custom Icon
					$img = $icon;
				}
				// Use Default Button
				else {
					// Set Default Icon
					$img = get_template_directory_uri() . $default;
				}

				// Add Link HTML
				$html .= '<a href="' . $href . '" target="_blank"><img src="' . $img . '" alt="" width="' . $size . '" height="' . $size . '" /></a>';
			}
		}
	}

	// Return Share HTML
	return $html;
}

/**
  * Shortcode for jrBlog Social Icons
  *
  * @author David A Conway Jr.
  * @since Real Chords 1.9
  */
function jrblog_socialicon_display( $atts ){
	// Extract Attributes
	extract( shortcode_atts( array(
		'force'     => 'true',
	), $atts ) );

	// Return HTML Code
    $html .= '<div class="jrblog_social_icons">';
    $html .= jrblog_follow_icons($force);
    $html .= '</div>';
    return $html;
}
add_shortcode( 'jrblog_socialicons', 'jrblog_socialicon_display' );

#######################################
## -- END SOCIAL FUNCTIONS
#######################################



#######################################
## -- START SEO FUNCTIONS
#######################################

/**
 * Disable WP Rel
 *
 * @author Whitney Krape
 * @src http://www.whitneykrape.com/2011/07/quick-fix-for-relcategory-tag-in-wordpress/ 
 * @since jrBlog 1.0
 */
function jrblog_norel_cat($text) {
	$text = str_replace('rel="category tag"', "", $text);
	return $text;
}
add_filter('the_category', 'jrblog_norel_cat');

#######################################
## -- END SEO FUNCTIONS
#######################################