<?php

namespace JR\Models;

use \JR\Models\Character;

class Game {

    public function __construct(\WP_Post $item, $relations = false)
    {
        ///////////////////////////////////
        // Inherit all $item properties  //
        ///////////////////////////////////
        foreach ((array) $item as $key => $value) {
            $this->{$key} = $value;
        }

        $this->name          = $this->post_title;
        $this->slug          = $this->post_name;
        $this->excerpt       = $this->post_excerpt;
        $this->permalink     = get_permalink($this->ID);

        // Url
        $this->use_full_url  = get_post_meta($this->ID, 'jrblog_social_full', true);
        $this->use_subdomain = get_post_meta($this->ID, 'jrblog_social_sub', true);
        $this->social_url    = get_post_meta($this->ID, 'jrblog_social_url', true);
        $this->username      = get_post_meta($this->ID, 'jrblog_social_name', true);

        // Options
        $this->is_shared     = get_post_meta($this->ID, 'jrblog_social_share', true);
        $this->is_sharing    = get_post_meta($this->ID, 'jrblog_social_sharing', true);
        $this->is_follow     = get_post_meta($this->ID, 'jrblog_social_follow', true);

        // Icon
        $icon_id             = get_post_meta($this->ID, 'icon_image', true);
        $this->has_icon      = !empty($icon_id);
        if ($this->has_icon){
            $image           = wp_get_attachment_image_src( $icon_id, 'thumbnail' );
            $this->icon      = $image[0];
        } else {
            $this->icon      = '';
        }


        ///////////////////////
        // Get relation data //
        ///////////////////////
        if(!empty($relations)) {
            // Character Data
            $characters     = array();
            $character_meta = get_post_meta($this->ID, 'associated_characters');
            if(!empty($character_meta) && !is_array($character_meta)) {
                $character_meta = array($character_meta);
            }
            if(is_array($character_meta)) {
                foreach($character_meta as $character) {
                    // Is Numeric? Generate Object
                    if(is_numeric($character)) {
                        $obj              = Character::get($character);
                        $obj->description = '';
                        $characters[]     = $obj;
                    }
                    else {
                        $item             = json_decode($character);
                        $obj              = Character::get($item->id);
                        $obj->description = \JR\Utils::parseMarkdown($item->description);
                        $characters[]     = $obj;
                    }
                }
            }
            $this->characters             = $characters;

            // Get Posts/Pages
            $this->reviews                = Reviews::filter(array('game_id' => $this->ID));
            $this->posts                  = Blog::filter(array('game_id' => $this->ID));
            $this->pages                  = Blog::filter(array('game_id' => $this->ID, 'is_page' => true));
        }
    }

    public static function get($id = null, $relations = false)
    {
        global $post_id;

        $id = $id ? $id : $post_id;

        return new Game(get_post($id), $relations);
    }
}