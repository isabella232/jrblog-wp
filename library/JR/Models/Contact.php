<?php

namespace GW\Models;

class Contact {

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

        // Custom Data
        $website             = get_post_meta($this->ID, 'website', true);
    }

    public static function get($id = null, $relations = false)
    {
        global $post_id;

        $id = $id ? $id : $post_id;

        return new Contact(get_post($id), $relations);
    }
}