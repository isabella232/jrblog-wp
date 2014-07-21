<?php

namespace JR\Models;

class PortfolioItem {

    public function __construct(\WP_Post $item, $relations = false)
    {
        ///////////////////////////////////
        // Inherit all $item properties  //
        ///////////////////////////////////
        foreach ((array) $item as $key => $value) {
            $this->{$key} = $value;
        }

        $this->name          = $this->post_title;
        $this->slug          = get_post_meta($this->ID, 'jrblog_social_slug', true);
        if(empty($this->slug)) {
            $this->slug      = $this->post_name;
        }
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
    }

    public static function get($id = null, $relations = false)
    {
        global $post_id;

        $id = $id ? $id : $post_id;

        return new PortfolioItem(get_post($id), $relations);
    }
}