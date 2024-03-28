<?php

use Ricubai\WPHelpers\ContentHelper;
use Ricubai\WPHelpers\FrontHelper;

if ( ! function_exists( 'echo_if' ) ) :
    /**
     * Helper for printing some string if a variable equals to a value.
     *
     * @param mixed  $var   Variable to compare.
     * @param mixed  $value Value to compare with variable.
     * @param string $print Output string in case variable equals passed value.
     */
    function echo_if( $var, $value, $print ) {
        echo $var === $value ? wp_kses_post( $print ) : '';
    }
endif;

if ( ! function_exists( 'echo_if_else' ) ) :
    /**
     * Helper for printing some string if a variable equals to a value, otherwise print else.
     *
     * @param mixed $var        Variable to compare.
     * @param mixed $value      Value to compare with variable.
     * @param mixed $print_if   Outputs string in case variable equals passed value.
     * @param mixed $print_else Outputs string in case variable does not equals passed value.
     */
    function echo_if_else( $var, $value, $print_if, $print_else ) {
        echo $var === $value ? wp_kses_post( $print_if ) : wp_kses_post( $print_else );
    }
endif;

if ( ! function_exists( 'get_cf_content' ) ) :
    /**
     * Helper for getting custom field content. Works for text, textarea, wysiwyg, etc. fields.
     * Will not work for fields which can return `false` value.
     * Checks if current page has value, then checks global content (option) value.
     *
     * @param string $cf_name Variable to compare.
     */
    function get_cf_content( $cf_name ) {
        $content = get_field( $cf_name, get_the_ID() );
        if ( $content ) {
            return $content;
        }
        $content = get_field( $cf_name, 'option' );
        if ( $content ) {
            return $content;
        }

        return null;
    }
endif;

if ( ! function_exists( 'get_cover_image_src' ) ) :
    /**
     * Helper for getting post cover image URL. Can be a WP featured image or from a custom field.
     *
     * @return string|false Taxonomy slug or false.
     */
    function get_cover_image_src() {
        return ContentHelper::get_cover_image_src() ?? false;
    }
endif;

if ( ! function_exists( 'the_cover_image' ) ) :
    /**
     * Helper for getting post cover image HTML tag. Can be a WP featured image or from a custom field.
     *
     * @return void Taxonomy slug or false.
     */
    function the_cover_image() : void {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo ContentHelper::get_cover_image();
    }
endif;

if ( ! function_exists( 'has_child_pages' ) ) :
    /**
     * Check if a page has child pages.
     */
    function has_child_pages( $post_id ) : bool {
        return FrontHelper::has_child_pages( (int) $post_id );
    }
endif;
