<?php
/**
 * @version   4.1.1 January 6, 2014
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Widget to show Related Characters.
 */

defined( 'GANTRY_VERSION' ) or die();

gantry_import( 'core.gantrywidget' );

add_action( 'widgets_init', array( 'jrBlogWidgetRelatedCharacters', 'init' ) );
add_action( 'save_post', array( 'jrBlogWidgetRelatedCharacters', 'gantry_flush_widget_cache' ) );

class jrBlogWidgetRelatedCharacters extends GantryWidget {
    var $short_name    = 'relatedcharacters';
    var $wp_name       = 'jrblog_relatedcharacters';
    var $long_name     = 'jrBlog Related Characters';
    var $description   = 'jrBlog Related Characters Widget';
    var $css_classname = 'widget_jrblog_relatedcharacters';
    var $width         = 200;
    var $height        = 400;

    static function gantry_flush_widget_cache( $post_id )
    {
        // If this isn't current post type, don't flush cache
        $types         = array('page', 'character');
        if ( empty($_POST['post_type']) || (!empty($_POST['post_type']) && !in_array($_POST['post_type'], $types) && $post_id)) {
            return;
        }

        wp_cache_delete( 'jrblog_relatedcharacters_post' . $post_id, 'widget' );
    }

    static function init() {
        register_widget( 'jrBlogWidgetRelatedCharacters' );
    }

    function get_post_object() {
        global $wp_query;
        $object          = $wp_query->get_queried_object();
        $id              = $object->ID;
        $post_type       = $object->post_type;

        // Get Blog Post
        switch($post_type) {
            case "post":
            case "page":
                $post = \JR\Models\BlogPost::get($id, true);
                break;
            case "game":
                $post = \JR\Models\Game::get($id, true);
                break;
            case "review":
                $post = \JR\Models\Review::get($id, true);
                break;
        }

        // Return Post Data
        return $post;
    }

    function is_available() {
        global $wp_query;
        $object          = $wp_query->get_queried_object();
        $id              = $object->ID;

        // Check Cache
        $cache = wp_cache_get('jrblog_relatedcharacters_post' . $id, 'widget');

        if( !is_array( $cache ) ) $cache = array();

        if( !isset( $cache[$args['widget_id']] ) ) {
            // Get Post Data
            $post        = $this->get_post_object();

            // Skip If Option Isn't Available for This Post Type
            if ( !in_array($post->post_type, array('post', 'page', 'game')) || empty($post->characters) ) {
                return false;
            }

            return true;
        }

        return $cache[$args['widget_id']];
    }

    function render_widget_open( $args, $instance ) {
        if(!$this->is_available()) {
            return;
        }

        echo $args['widget_open'];
    }
    
    function render_widget_close( $args, $instance ) {
        if(!$this->is_available()) {
            return;
        }

        echo $args['widget_close'];
    }
    
    function pre_render( $args, $instance ) {
        if(!$this->is_available()) {
            return;
        }

        echo $args['pre_render'];
    }
    
    function post_render( $args, $instance ) {
        if(!$this->is_available()) {
            return;
        }

        echo $args['post_render'];
    }

    function render_title( $args, $instance ) {
        if(!$this->is_available()) {
            return;
        }

        /** @global $gantry Gantry */
        global $gantry;
        if( $instance['title'] != '' ) :
            echo $instance['title'];
        endif;
    }

    function render( $args, $instance ) {
        global $gantry, $characters, $character, $wp_query;

        if($cache = $this->is_available()) {
            if($cache !== true) {
                echo $cache;
                return;
            }
        }
        // Skip If Option Isn't Available for This Post Type
        else {
            return;
        }

        // Get Post data
        $post            = $this->get_post_object();

        ob_start();

        $menu_class = $instance['menu_class'];
        $link_class = $instance['link_class'];

        if($menu_class != '') :
            $menu_class = ' class="menu ' . $menu_class . '"'; else :
            $menu_class = ' class="menu"';
        endif;

        if($link_class != '') :
            $link_class = ' class="' . $link_class . '"'; else :
            $link_class = '';
        endif;

        $output = '';

        if( !$number = ( int ) $instance['number'] ) $number = 0;

        $characters = $post->characters;

        $output .= '<ul' . $menu_class . '>';

        if( $characters ) {
            $num = 0;
            foreach ( ( array ) $characters as $character ) {
                if($number > 0 && $number <= $num) {
                    break;
                }

                $name    = $character->name;

                $output .= '<li class="character-item">';
                $output .= '<a href="' . $character->permalink . '"' . $link_class . '>';
                $output .= '<span>' . $character->name . '</span>';
                $output .= '</a>';
                $output .= '</li>';

                $num++;
            }
        }

        $output .= '</ul>';

        echo $output;

        $cache[$args['widget_id']] = $output;

        wp_cache_set( 'jrblog_relatedcharacters_post' . $id, $cache, 'widget' );

        echo ob_get_clean();
    }
}