<?php

namespace Ricubai\WPHelpers;

class WpBlocksHelper {
    public static function register_block( $block_path ) : void {
        add_action(
            'init',
            function() use ( $block_path ) {
                register_block_type( $block_path );
            }
        );
    }

    /**
     * Add Gutenberg block styles.
     *
     * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-styles/
     *
     * @param string $block_name Block name. Like 'core/button'.
     * @param array  $args       Style properties:
     *                           'name'  => 'custom-button',
     *                           'label' => 'Custom Button',
     *                           // Optionally, you can add inline_style or style_handle if needed.
     *                           'inline_style' => '.wp-block-button.is-style-custom-button { your CSS }
     *                           'style_handle' => 'your_custom_styles_handle'
     *                           'is_default' => true
     *
     * @return void
     */
    public static function add_block_styles( $block_name, $args = [] ) : void {
        $args = wp_parse_args(
            $args,
            []
        );
        add_action(
            'init',
            function() use ( $block_name, $args ) {
                register_block_style( $block_name, $args );
            }
        );
    }

    /**
     * Add Gutenberg block category.
     *
     * @link https://developer.wordpress.org/reference/hooks/block_categories_all/
     *
     * @param array $args Category properties:
     *                    'slug'  => 'custom-layout-category',
     *                    'title' => 'Layout',
     *                    'icon'  => null
     *
     * @return void
     */
    public static function add_editor_block_category( array $args = [] ) : void {
        $args = wp_parse_args(
            $args,
            [
                'icon' => null,
            ]
        );
        add_filter(
            'block_categories_all',
            function( $categories ) use ( $args ) {
                // Adding a new category in the beginning.
                array_unshift( $categories, $args );

                return $categories;
            }
        );
    }

    /**
     * Add Gutenberg block pattern category.
     *
     * @link https://developer.wordpress.org/reference/functions/register_block_pattern_category/
     *
     * @param string $slug Category slug.
     * @param array  $args Category properties:
     *                     'label'       => 'Custom Patterns',
     *                     'description' => 'Custom Patterns Description',
     *
     * @return void
     */
    public static function add_editor_pattern_category( $slug, array $args = [] ) : void {
        $args = wp_parse_args(
            $args,
            [
                'label'       => null,
                'description' => null,
            ]
        );
        add_action(
            'init',
            function() use ( $slug, $args ) {
                register_block_pattern_category( $slug, $args );
            }
        );
    }

    /**
     * Add color palette to Gutenberg editor. Merges default color palette with custom colors.
     *
     * @param array $palette Custom color palette. Format:
     *                       array(
     *                       'name'  => 'Color name',
     *                       'slug'  => 'theme-color-slug',
     *                       'color' => '#F1F7FB',
     *                       )
     *
     * @return void
     */
    public static function add_editor_color_palette( array $palette = [] ) : void {
        add_action(
            'after_setup_theme',
            function() use ( $palette ) {
                $default_color_palette = [];
                //
                // Get default core color palette from wp-includes/theme.json
                if ( class_exists( 'WP_Theme_JSON_Resolver' ) ) {
                    $settings = WP_Theme_JSON_Resolver::get_core_data()->get_settings();
                    if ( isset( $settings['color']['palette']['default'] ) ) {
                        $default_color_palette = $settings['color']['palette']['default'];
                    }
                }
                // Add custom palettes merged with existing.
                add_theme_support(
                    'editor-color-palette',
                    array_merge( $default_color_palette, array( $palette ) )
                );
            }
        );
    }

    public static function remove_block_default_styles( array $args = [] ) : void {
        $args = wp_parse_args(
            $args,
            [
                'except' => [],
            ]
        );
        add_action(
            'enqueue_block_editor_assets',
            static function() {
                $script = <<< JS
                window.onload = function() {
                    wp.domReady( () => {
                        wp.blocks.unregisterBlockStyle( 'core/image', 'default' );
                        wp.blocks.unregisterBlockStyle( 'core/image', 'rounded' );
                        wp.blocks.unregisterBlockStyle( 'core/button', 'fill' );
                        wp.blocks.unregisterBlockStyle( 'core/button', 'outline' );
                    });
                }
                JS;
                wp_add_inline_script( 'wp-blocks', $script );
            }
        );
    }

    public static function disable_editor_fullscreen() : void {
        add_action(
            'enqueue_block_editor_assets',
            static function() {
                $script = <<< JS
                window.onload = function() {
                    const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' );
                    console.log( isFullscreenMode );
                    if ( isFullscreenMode ) {
                        wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' );
                    }
                }
                JS;
                wp_add_inline_script( 'wp-blocks', $script );
            }
        );
    }
}
