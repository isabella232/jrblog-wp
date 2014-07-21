<?php

namespace JR\Models;

use \JR\Models\BlogPost;

class Blog {
    
    /**
     * Filters person post type entries
     * 
     * @param  array  $filters Associative array of filters
     * @return array           Array of \TR\Models\BlogPost objects
     */
    public static function filter(array $filters)
    {
        $args = array(
            'numberposts' => -1,
            'order'       => 'DESC',
            'orderby'     => 'post_date',
            'post_type'   => 'post'
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


        if (isset($filters['is_page']) && !empty($filters['is_page'])) {
            $args['post_type'] = 'page';
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


        if (isset($filters['game_id'])) {

            if(empty($args['meta_query']) || is_array($args['meta_query'])) {
                $args['meta_query'] = array();
            }
            $args['meta_query'][] = array(
                'key'     => 'associated_games',
                'value'   => serialize(strval($filters['game_id'])),
                'compare' => 'LIKE'
            );
        }

        if (isset($filters['character_id'])) {

            if(empty($args['meta_query']) || is_array($args['meta_query'])) {
                $args['meta_query'] = array();
            }
            $args['meta_query'][] = array(
                'key'     => 'associated_characters',
                'value'   => serialize(strval($filters['character_id'])),
                'compare' => 'LIKE'
            );
        }


        $items = get_posts($args);

        $parsed_items = array();
        if ($items) 
        {
            foreach ($items as $item) 
            {
                $parsed_items[] = new BlogPost($item);
            }
        }

        return $parsed_items;
    }
}

