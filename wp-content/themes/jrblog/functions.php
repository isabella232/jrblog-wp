<?php
/**
 * @version   2.0.1 May 28, 2014
 * @author    JaidynReiman http://www.jrconway.net
 * @copyright Copyright (C) 2014 JaidynReiman
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


#######################################
## -- START BEBOP INITIALIZATION
#######################################

use \Ponticlaro\Bebop;
use \JR\Models\Contacts;

date_default_timezone_set('America/Chicago');

///////////////////////////
// Autoload dependencies //
///////////////////////////
require_once ABSPATH . '/vendor/autoload.php';

////////////////
// Boot Bebop //
////////////////
Bebop::boot();

//////////////////////
// Set project urls //
//////////////////////
Bebop::setUrl(array(
    ////////////
    // Assets //
    ////////////
    "theme_css"    => Bebop::getUrl("theme", "css"),
    "theme_less"   => Bebop::getUrl("theme", "less"),
    "theme_js"     => Bebop::getUrl("theme", "js"),
    "theme_images" => Bebop::getUrl("theme", "img"),
    "theme_icons"  => Bebop::getUrl("theme", "img/icons")
));

///////////////////////
// Set project paths //
///////////////////////
/*Bebop::setPath(array(
    ""             => Bebop::getPath("theme", "")
));*/


///////////////////////////
// Set custom post types //
///////////////////////////

// Social
$social_post_type = Bebop::PostType(array('Social', 'Social Links'), array(
    'supports'    => array(
        'title'
    )
));


//////////////////////
// Custom Metaboxes //
//////////////////////

// Page: Video
$metafields = array(
    'jrblog_page_video_src',
    'jrblog_page_video_id'
);

$page_video_metabox = Bebop::Metabox('Video', array('post', 'page'), $metafields, function($data, $entry) {

        $src = $data->get('jrblog_page_video_src');
        $id  = $data->get('jrblog_page_video_id');
    ?>

    <label for="">Video Source</label><br>
    <select name="jrblog_page_video_src">
        <option value="youtube" <?php echo (($src == 'youtube') ? ' selected="selected"' : ''); ?>>Youtube</option>
        <option value="vimeo" <?php echo (($src == 'vimeo') ? ' selected="selected"' : ''); ?>>Vimeo</option>
    </select><br /><br />

    <label for="">Video ID</label><br>
    <p><small><em>(ID of the Youtube or Vimeo Video. The Video will replaced the Featured Image on the Blog.)</em></small></p>
    <input type="text" class="large-text" name="jrblog_page_video_src" value="<?php echo $name; ?>">

<?php }, array(
    'context'  => 'side'
));


// Page: Options
$metafields = array(
    'jrblog_page_disable_excerpt'
);

$page_options_metabox = Bebop::Metabox('Options', array('post', 'page'), $metafields, function($data, $entry) {

        $excerpt = $data->get('jrblog_page_disable_excerpt');
    ?>

    <label for="">Disable Text Excerpt</label><br>
    <p><small><em>(When checked, a text excerpt will not be included with the featured image or video. If a Featured Image or Video is not provided the Text Excerpt will still be Used.)</em></small></p>
    <input type="checkbox" name="jrblog_page_disable_excerpt" value="1" <?php echo (!empty($excerpt) ? ' checked="checked"' : ''); ?> /><br><br>

<?php }, array(
    'context'  => 'side'
));



// Social: Icon Image
$metafields = array(
    'icon_image'
);

$social_icon_metabox = Bebop::Metabox('Icon', $social_post_type, $metafields, function($data, $entry) { ?>

    <?php Bebop::UI()->Media('Social Icon', $data->get('icon_image'), array(
        'field_name' => 'icon_image',
        'mime_types' => array('image')
    ))->render(); ?>

<?php }, array(
    'context'  => 'side'
));


// Social: Details
$metafields = array(
    'jrblog_social_full',
    'jrblog_social_slug',
    'jrblog_social_sub',
    'jrblog_social_url',
    'jrblog_social_name'
);

$social_url_metabox = Bebop::Metabox('URL', $social_post_type, $metafields, function($data, $entry) {

        $full = $data->get('jrblog_social_full') ?: false;
        $slug = $data->get('jrblog_social_slug') ?: $entry->post_name;
        $sub  = $data->get('jrblog_social_sub');
        $url  = $data->get('jrblog_social_url');
        $name = $data->get('jrblog_social_name');
    ?>

    <label for="">Use Full Social Media URL?</label><br>
    <input type="checkbox" name="jrblog_social_full" value="1" <?php echo (!empty($full) ? ' checked="checked"' : ''); ?> /><br><br>

    <label for="">Use Social Media URL as Subdomain?</label><br>
    <input type="checkbox" name="jrblog_social_sub" value="1" <?php echo (!empty($sub) ? ' checked="checked"' : ''); ?> /><br><br>

    <label for="">Social Media URL</label><br>
    <input type="text" class="large-text" name="jrblog_social_url" value="<?php echo $url; ?>"><br><br>

    <label for="">Social Media Username</label><br>
    <input type="text" class="large-text" name="jrblog_social_name" value="<?php echo $name; ?>">

<?php }, array(
    'context'  => 'side'
));


// Social: Options
$metafields = array(
    'jrblog_social_share',
    'jrblog_social_sharing',
    'jrblog_social_follow'
);

$social_options_metabox = Bebop::Metabox('Options', $social_post_type, $metafields, function($data, $entry) {

        $share   = $data->get('jrblog_social_share') ?: false;
        $sharing = $data->get('jrblog_social_sharing') ?: false;
        $follow  = $data->get('jrblog_social_follow') ?: false;
    ?>

    <label for="">Display Share Button?</label><br>
    <input type="checkbox" name="jrblog_social_sharing" value="1" <?php echo (!empty($sharing) ? ' checked="checked"' : ''); ?> /><br><br>

    <label for="">Display Follow Button?</label><br>
    <input type="checkbox" name="jrblog_social_follow" value="1" <?php echo (!empty($follow) ? ' checked="checked"' : ''); ?> />

<?php }, array(
    'context'  => 'side'
));


#######################################
## -- END BEBOP INITIALIZATION
#######################################


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

if ( ! function_exists( 'jrblog_custom_contact' ) ) :
/**
 * Set Custom Contact Fields
 *
 * @since jrBlog 2.0.1
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
 * Update Setting on Theme Switch
 *
 * @since jrBlog 2.0.1
 */
function jrblog_switch_theme() {
    // Get Existing Versions
    $versions   = get_option('jrblog_theme_versions', array());

    // Version 2.0.1
    if(!in_array('2.0.1', $versions)) {
        // Set Version
        $versions[] = '2.0.1';

        // Add Contact Fields
        $contacts   = jrblog_custom_contact();
        $num        = 0;
        foreach($contacts as $key => $contact) {
            // No Contact Data?
            if(empty($contact)) {
                // Skip
                continue;
            }

            // Insert Post
            $post = array(
                'post_name'      => $key,
                'post_title'     => $contact['name'],
                'post_status'    => 'publish',
                'post_type'      => 'social',
                'ping_status'    => 'closed',
                'menu_order'     => $num,
                'comment_status' => 'closed'
            );  
            $id = wp_insert_post( $post, $wp_error );
            update_post_meta($id, 'jrblog_social_slug', $key);
            update_post_meta($id, 'jrblog_social_url', $contact['url']);
            update_post_meta($id, 'jrblog_social_share', $contact['share']);
            update_post_meta($id, 'jrblog_social_sub', $contact['sub']);
            $num++;
        }
    }

    // Update Set Versions
    update_option('jrblog_theme_versions', $versions);
}
add_action('after_switch_theme', 'jrblog_switch_theme');

if ( ! function_exists( 'jrblog_schema' ) ):
/**
 * Gets Site Schema From Theme Options
 *
 * @since jrBlog 2.0.1
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
 * @since jrBlog 2.0.1
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
 * @since jrBlog 2.0.1
 */
function jrblog_share_buttons($url = '', $text = '') {
    // All Sharing Disabled?
    if(1) {
        return '';
    }

	// Get Social Sites
    $fields = Contacts::filter(array("is_shared" => true));
	$html   = '';

	// Loop Social Sites
	foreach($fields as $field) {
		// Set Unique Variables
		$func  = 'jrblog_' . $field->slug . '_button';

		// Add to Contact Methods
		if(!empty($field->is_sharing) && function_exists($func)) {
			// Get Share Button
			$html .= $func($url, $text);
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
 * @since jrBlog 2.0.1
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
 * @since jrBlog 2.0.1
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
 * @since jrBlog 2.0.1
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
 * @since jrBlog 2.0.1
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
 * @since jrBlog 2.0.1
 */
function jrblog_follow_icons($force = false) {
    global $gantry;

    // Get Social Sites
    $fields = Contacts::filter(array("is_follow" => true));
    $size   = $gantry->get('social-icons');
    $html   = '';

    // All Links Disabled?
    if((!is_array($fields) || !count($fields)) && empty($force)) {
        return '';
    }


    // Loop Social Sites
    foreach($fields as $code => $field) {
        // Valid Site?
        if(!empty($field->ID)) {
            // Set Unique Variables
            $img     = '';
            $href    = '';
            $sub     = $field->use_subdomain;
            $url     = $field->social_url;
            $acct    = $field->username;
            $icon    = $field->icon;
            $follow  = $field->is_follow;
            $default = '/images/icons/' . $size . '/' . $field->slug . '.png';

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
            if(!empty($url) && ($field->use_full_url || !empty($acct))) {
                // Use Custom Button?
                if(!empty($icon)) {
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
 * @since jrBlog 2.0.1
 */
function jrblog_norel_cat($text) {
	$text = str_replace('rel="category tag"', "", $text);
	return $text;
}
add_filter('the_category', 'jrblog_norel_cat');

#######################################
## -- END SEO FUNCTIONS
#######################################