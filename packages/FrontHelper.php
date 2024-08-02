<?php
/**
 * Helpers related to WordPress front-end. It's more general and not specific to content.
 * - Changing <body> class
 * - Changing nav classes
 * - Getting category tree
 * - Getting term tree
 * - Checking if a page has child pages
 */

namespace Ricubai\WPHelpers;

use WP_Post_Type;

/**
 * Class FrontHelper
 *
 * @package Ricubai\WPHelpers
 */
class FrontHelper {
    public static function body_class( $class ) : void {
        add_filter(
            'body_class',
            static function( $classes ) use ( $class ) {
                $classes[] = $class;

                return $classes;
            },
            10,
            2
        );
    }

    public static function get_category_tree( $args = '' ) {
        $defaults = array(
            'taxonomy' => 'category',
        );

        $args = wp_parse_args( $args, $defaults );

        return self::get_term_tree( $args );
    }

    public static function get_term_tree( $args = '' ) {
        $defaults = array(
            'hide_empty'   => true,
            'echo'         => false,
            'hierarchical' => 1,
            'show_count'   => false,
            'parent'       => 0,
            'orderby'      => 'name',
            'order'        => 'ASC',
            'tree'         => [],
        );

        $args = wp_parse_args( $args, $defaults );

        $cats = get_terms( $args );

        if ( $cats ) {
            foreach ( $cats as &$cat ) {
                $args['parent'] = $cat->term_id;
                $cat->childs    = self::get_term_tree( $args );
            }
        }

        return $cats;
    }

    /**
     * Check if a page has child pages.
     *
     * @param int $post_id The post ID.
     *
     * @return bool
     */
    public static function has_child_pages( $post_id ) {
        // Query child pages.
        $child_pages = get_pages( array(
            'child_of'    => $post_id,
            'post_type'   => 'page',
            'post_status' => 'publish',
        ) );

        if ( $child_pages ) {
            return true;
        }

        return false;
    }

    /**
     * Get current post type archive page taxonomy type.
     */
    public static function get_current_archive_taxonomy_type() {
        // Check if current page is built-in type of the archive page.
        if ( is_category() || is_home() ) {
            return 'category';
        }

        if ( function_exists( 'is_shop' ) && is_shop() ) {
            return 'product_cat';
        }

        if ( is_tag() ) {
            return 'post_tag';
        }

        if ( is_tax() ) {
            return get_query_var( 'taxonomy' );
        }

        if ( is_post_type_archive() ) {
            // Get post type for current page query.
            $post_type  = self::get_current_post_type();
            $taxonomies = get_object_taxonomies( $post_type );

            return $taxonomies[0] ?? null;
        }
    }

    /**
     * Get post type for current page query.
     * Works for single post, archive, taxonomy, etc.
     */
    public static function get_current_post_type() {
        $post_type = get_query_var( 'post_type' );

        if ( ! $post_type ) {
            $tax = get_query_var( 'taxonomy' );
            if ( $tax ) {
                $cpts      = self::get_taxonomy_cpts( $tax );
                $post_type = $cpts[0] ?? null;
            } // Handle cases where neither post_type nor taxonomy is set
            elseif ( is_home() || is_category() || is_tag() || is_date() ) {
                $post_type = 'post';
            } elseif ( is_attachment() ) {
                $post_type = 'attachment';
            } elseif ( is_page() ) {
                $post_type = 'page';
            } elseif ( is_singular() ) {
                $post_type = get_post_type();
            } elseif ( is_search() || is_404() ) {
                $post_type = null; // or you could set a specific value for these cases
            } else {
                // Fall back to the main queried object
                $queried_object = get_queried_object();
                $post_type      = $queried_object instanceof WP_Post_Type ? $queried_object->name : null;
            }
        }

        return $post_type;
    }

    /**
     * Get post type label by post type name.
     */
    public static function get_post_type_label( $post_type ) : string {
        $post_type_obj = get_post_type_object( $post_type );

        return $post_type_obj->labels->name ?? '';
    }

    /**
     * Get all associated post types for given taxonomy.
     */
    public static function get_taxonomy_cpts( $tax ) {
        $all_post_types        = get_post_types( array(), 'objects' );
        $associated_post_types = array();

        foreach ( $all_post_types as $post_type_obj ) {
            $taxonomies = get_object_taxonomies( $post_type_obj->name );
            if ( in_array( $tax, $taxonomies ) ) {
                $associated_post_types[] = $post_type_obj->name;
            }
        }

        return $associated_post_types;
    }

}
