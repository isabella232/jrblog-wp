<?php

namespace GW\Models;

use \GW\Models\Contact;

class Contacts {
    
    /**
     * Filters person post type entries
     * 
     * @param  array  $filters Associative array of filters
     * @return array           Array of \GW\Models\Contact objects
     */
    public static function filter(array $filters)
    {
        $args = array(
            'numberposts' => -1,
            'order'       => 'ASC',
            'post_type'   => 'affiliate'
        );

        if (isset($filters['numberposts'])) {
            $args['numberposts'] = $filters['numberposts'];
        }

        if (isset($filters['orderby'])) {
            $args['orderby'] = $filters['orderby'];
        }

        if (isset($filters['order'])) {
            $args['order'] = $filters['order'];
        }


        $items = get_posts($args);

        $parsed_items = array();
        if ($items) 
        {
            foreach ($items as $item) 
            {
                $parsed_items[] = new Contact($item);
            }
        }

        return $parsed_items;
    }
}

