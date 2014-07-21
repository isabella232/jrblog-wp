<?php
/**
 * @version   4.1.1 January 6, 2014
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Widget to show Related Games.
 */

defined( 'GANTRY_VERSION' ) or die();

gantry_import( 'core.gantrywidget' );

add_action( 'widgets_init', array( 'jrBlogWidgetRelatedGames', 'init' ) );
add_action( 'save_post', array( 'jrBlogWidgetRelatedGames', 'gantry_flush_widget_cache' ) );

class jrBlogWidgetRelatedGames extends GantryWidget {
    var $short_name    = 'relatedgames';
    var $wp_name       = 'jrblog_relatedgames';
    var $long_name     = 'jrBlog Related Games';
    var $description   = 'jrBlog Related Games Widget';
    var $css_classname = 'widget_jrblog_relatedgames';
    var $width         = 200;
    var $height        = 400;

    static function gantry_flush_widget_cache( $post_id )
    {
        // If this isn't current post type, don't flush cache
        $types         = array('page', 'game');
        if ( empty($_POST['post_type']) || (!empty($_POST['post_type']) && !in_array($_POST['post_type'], $types) && $post_id)) {
            return;
        }

        wp_cache_delete( 'jrblog_relatedgames_post' . $post_id, 'widget' );
    }

    static function init() {
        register_widget( 'jrBlogWidgetRelatedGames' );
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
        $cache = wp_cache_get('jrblog_relatedgames_post' . $id, 'widget');

        if( !is_array( $cache ) ) $cache = array();

        if( !isset( $cache[$args['widget_id']] ) ) {
            // Get Post Data
            $post        = $this->get_post_object();

            // Skip If Option Isn't Available for This Post Type
            if ( !in_array($post->post_type, array('post', 'page')) || empty($post->games) ) {
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
        global $gantry, $games, $game, $wp_query;

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

        if( !$number = ( int ) $instance['number'] ) $number = 5; else if( $number < 1 ) $number = 1;

        $games = $post->games;

        $output .= '<ul' . $menu_class . '>';

        if( $games ) {
            $num = 0;
            foreach ( ( array ) $games as $game ) {
                if($number > 0 && $number <= $num) {
                    break;
                }

                $name    = $game->name;

                $output .= '<li class="game-item">';
                $output .= '<a href="' . $game->permalink . '"' . $link_class . '>';
                $output .= '<span>' . $game->name . '</span>';
                $output .= '</a>';
                $output .= '</li>';

                $num++;
            }
        }

        $output .= '</ul>';

        echo $output;

        $cache[$args['widget_id']] = $output;

        wp_cache_set( 'jrblog_relatedgames_post' . $id, $cache, 'widget' );

        echo ob_get_clean();
    }
}