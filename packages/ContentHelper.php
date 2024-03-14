<?php

namespace Ricubai\WPHelpers;

/**
 * Class ContentHelper
 *
 * @package Ricubai\WPHelpers
 */
class ContentHelper {
    /**
     * @var string[] $cover_image_field_order Array of cover image field names to check in the provided order. By default checks firstly a custom field `cover_image` and then a featured image.
     */
    private static array $cover_image_field_order = [ 'cover_image', 'wp_featured_image' ];

    /**
     * Set cover image field source. It can be a custom field or a featured image.
     *
     * @param string|array $args
     *
     * @return void
     */
    public static function set_cover_image_field_source( $args ) : void {
        $args = wp_parse_args( $args, [
            'wp_featured_image_only' => false, // If true, then only featured image will be used as a cover image.
        ] );
        if ( (bool) $args['wp_featured_image_only'] ) {
            self::$cover_image_field_order = [ 'wp_featured_image' ];
        }
    }

    public static function get_cover_image_src() {
        $image_url = null;
        foreach ( self::$cover_image_field_order as $source_name ) {
            if ( $source_name === 'wp_featured_image' ) {
                $image_url = get_the_post_thumbnail_url( null, 'full' );
            } else {
                $image_url = get_cf_content( $source_name );
            }

            // If there is a cover image for $source_name, then break the loop.
            if ( $image_url ) {
                break;
            }
        }

        return $image_url;
    }

    public static function get_cover_image() {
        $image = null;
        foreach ( self::$cover_image_field_order as $source_name ) {
            if ( $source_name === 'wp_featured_image' ) {
                $image = get_the_post_thumbnail( null, 'full', [ 'class' => 'cover-image' ] );
            } else {
                $image_url = get_cf_content( $source_name );
                if ( $image_url ) {
                    $image = '<img data-src="' . esc_url( $image_url ) . '" class="cover-image lazysrc" alt="" decoding="async" fetchpriority="high" />';
                }
            }

            // If there is a cover image for $source_name, then break the loop.
            if ( $image ) {
                break;
            }
        }

        return $image;
    }

    public static function year_shortcode() : void {
        add_shortcode(
            'year',
            static function() {
                return current_time( 'Y' );
            }
        );
    }

}
