<?php

namespace JR\Models;

use \JR\Models\PortfolioItem;

class Portfolio {
    
    /**
     * Filters person post type entries
     * 
     * @param  array  $filters Associative array of filters
     * @return array           Array of \TR\Models\PortfolioItem objects
     */
    public static function filter(array $filters)
    {
        $args = array(
            'numberposts' => -1,
            'order'       => 'DESC',
            'orderby'     => 'post_date',
            'post_type'   => 'portfolio'
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


        if (isset($filters['is_shared'])) {

            if(empty($args['meta_query']) || is_array($args['meta_query'])) {
                $args['meta_query'] = array();
            }
            $args['meta_query'][] = array(
                'key'     => 'jrblog_social_sharing',
                'value'   => $filters['is_shared'],
                'compare' => '=',
                'type'    => 'BINARY'
            );
        }


        if (isset($filters['is_follow'])) {

            if(empty($args['meta_query']) || is_array($args['meta_query'])) {
                $args['meta_query'] = array();
            }
            $args['meta_query'][] = array(
                'key'     => 'jrblog_social_follow',
                'value'   => $filters['is_follow'],
                'compare' => '=',
                'type'    => 'BINARY'
            );
        }


        $items = get_posts($args);

        $parsed_items = array();
        if ($items) 
        {
            foreach ($items as $item) 
            {
                $parsed_items[] = new PortfolioItem($item);
            }
        }

        return $parsed_items;
    }
}

