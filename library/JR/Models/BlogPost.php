<?php

namespace JR\Models;

use \JR\Models\Character;
use \JR\Models\Game;

class BlogPost {

    public function __construct(\WP_Post $item, $relations = false)
    {
        global $gantry;

        ///////////////////////////////////
        // Inherit all $item properties  //
        ///////////////////////////////////
        foreach ((array) $item as $key => $value) {
            $this->{$key} = $value;
        }

        $this->name          = $this->post_title;
        $this->slug          = $this->post_name;
        $this->content       = $this->post_content;
        $this->excerpt       = $this->post_excerpt;
        $this->permalink     = get_permalink($this->ID);

        // Options
        $this->use_video     = false;
        $this->use_both      = true;
        $this->use_excerpt   = true;
        $this->video         = new \stdClass;

        // Url
        $this->video->src    = get_post_meta($this->ID, 'jrblog_page_video_src', true);
        $this->video->id     = get_post_meta($this->ID, 'jrblog_page_video_id', true);
        $this->video->list   = get_post_meta($this->ID, 'jrblog_page_video_list', true);
        $this->video->url    = '';
        $this->video->embed  = '';
        $this->video->html   = '';

        // Valid Video?
        if(!empty($this->video->id)) {
            // Youtube Video
            if($this->video->src == 'youtube') {
                $this->video->url        = 'https://www.youtube.com/watch?v=' . $this->video->id;
                $this->video->embed      = '//www.youtube.com/embed/' . $this->video->id;

                // Is a Playlist?
                if(!empty($this->video->list)) {
                    $this->video->url   .= '&list=' . $this->video->list;
                    $this->video->embed .= '?list=' . $this->video->list . '&wmode=transparent';
                }
                else {
                    $this->video->embed .= '?wmode=transparent';
                }
            }
            // Vimeo Video
            elseif($this->video->src == 'vimeo') {
                $this->video->url        = 'http://vimeo.com/' . $this->video->id;
                $this->video->embed      = '//player.vimeo.com/video/' . $this->video->id;

                // Is a Playlist?
                if(!empty($this->video->list)) {
                    $this->video->url    = 'http://vimeo.com/album/' . $list->id . '/video' . $this->video->id;
                }
            }

            // Create Embed HTML
            if(!empty($this->video->embed)) {
                $this->video->html       = '<div class="jrblog_video_wrapper"><iframe src="' . $this->video->embed . '" frameborder="0" allowfullscreen></iframe></div>';
                $this->use_video = true;
            }
        }

        // Skip Excerpt
        $skip              = get_post_meta( get_the_ID(), 'jrblog_page_disable_excerpt', true);
        if(!empty($skip)) {
            $this->use_excerpt = false;
        }

        // Featured Image/Video Position
        $pos               = get_post_meta( get_the_ID(), 'jrblog_page_featured_position', true);
        if(empty($pos) || $pos == 'gantry') {
            if(!empty($gantry)) {
                $pos = $gantry->get( 'thumb-position', 'left' );
            }
            $pos = 'full';
        }
        $this->feature_position = $pos;

        // Thumbnail
        $thumbnail_id        = get_post_thumbnail_id($this->ID);
        $this->has_thumbnail = has_post_thumbnail($this->ID);
        if ($this->has_icon){
            $image           = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
            $this->thumbnail = $image[0];
        } else {
            $this->thumbnail = '';
        }


        ///////////////////////
        // Get relation data //
        ///////////////////////
        if(!empty($relations)) {
            // Character Data
            $characters       = array();
            $character_meta   = get_post_meta($this->ID, 'associated_characters', array());
            if(!empty($character_meta) && !is_array($character_meta)) {
                $character_meta = array($character_meta);
            }
            if(is_array($character_meta)) {
                foreach($character_meta as $character) {
                    $characters[] = Character::get($character);
                }
            }
            $this->characters     = $characters;

            // Game Data
            $games       = array();
            $game_meta   = get_post_meta($this->ID, 'associated_games', array());
            if(!empty($game_meta) && !is_array($game_meta)) {
                $game_meta = array($game_meta);
            }
            if(is_array($game_meta)) {
                foreach($game_meta as $game) {
                    $games[]      = Game::get($game);
                }
            }
            $this->games          = $games;
        }
    }

    public static function get($id = null, $relations = false)
    {
        global $post_id;

        $id = $id ? $id : $post_id;

        return new BlogPost(get_post($id), $relations);
    }
}