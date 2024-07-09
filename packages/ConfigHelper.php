<?php
/**
 * Helpers related to WordPress configuration including removing unnecessary headers, scripts, etc.
 * Or enabling/disabling certain features.
 */

namespace Ricubai\WPHelpers;

/**
 * Class ConfigHelper
 *
 * @package Ricubai\WPHelpers
 */
class ConfigHelper {
    public static function disable( $disable ) {
        $disable = (array) $disable;
        foreach ( $disable as $item ) {
            switch ( $item ) {
                case 'wp_generator':
                    remove_action( 'wp_head', 'wp_generator' );
                    break;
                case 'rsd_link':
                    remove_action( 'wp_head', 'rsd_link' );
                    break;
                case 'feed_links':
                    remove_action( 'wp_head', 'feed_links', 2 );
                    break;
                case 'feed_links_extra':
                    remove_action( 'wp_head', 'feed_links_extra' );
                    break;
                case 'wlwmanifest_link':
                    remove_action( 'wp_head', 'wlwmanifest_link' );
                    break;
                case 'print_emoji_detection_script':
                    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
                    break;
                case 'rest_output_link_wp_head':
                    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
                    break;
                case 'wp_shortlink_wp_head':
                    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
                    break;
                case 'print_emoji_styles':
                    remove_action( 'wp_print_styles', 'print_emoji_styles' );
                    break;
                case 'wp_shortlink_header': // remove Link header for shortlink.
                    remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
                    break;
                case 'pings_open': // remove X-Pingback header.
                    add_filter( 'pings_open', '__return_false' );
                    break;
                case 'src_version':
                    add_filter( 'script_loader_src', '\Ricubai\WPHelpers\ConfigHelper::remove_src_version', 9999 );
                    add_filter( 'style_loader_src', '\Ricubai\WPHelpers\ConfigHelper::remove_src_version', 9999 );
                    break;
                case 'xmlrpc_enabled':
                    add_filter( 'xmlrpc_enabled', '__return_false' );
                    break;
                case 'wp_resource_hints':
                    add_filter( 'wp_resource_hints', '\Ricubai\WPHelpers\ConfigHelper::empty_array', 20 );
                    break;
                case 'remove_unnecessary_headers':
                    add_filter( 'wp_headers', '\Ricubai\WPHelpers\ConfigHelper::remove_unnecessary_headers', 999 );
                    break;
                case 'rest_output_link_header': // Disable sending a Link header for the REST API.
                    remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
                    break;
                case 'admin_bar': // Disable admin bar.
                    add_filter( 'show_admin_bar', '__return_false' );
                    break;
                case 'wp_global_styles': // Disable frontend inline global styles.
                    add_filter(
                        'wp_enqueue_scripts',
                        function() {
                            ConfigHelper::dequeue_script( 'global-styles' );
                        },
                        999
                    );
                    break;
                case 'wp_block_styles': // Disable frontend inline global styles.
                    add_filter(
                        'wp_enqueue_scripts',
                        function() {
                            ConfigHelper::dequeue_script( 'wp-block-library' );
                            ConfigHelper::dequeue_script( 'wp-block-library-theme' );
                            // REMOVE WOOCOMMERCE BLOCK CSS?
                            ConfigHelper::dequeue_script( 'wc-block-style' );
                        },
                        999
                    );
                    break;
                default:
                    break;
            }
        }

        //remove_filter( 'the_content', 'wptexturize' );
        //remove_filter( 'the_content', 'wpautop' );
    }

    public static function disableAllExcept( $exclude ) {
        $all    = [
            'wp_generator',
            'rsd_link',
            'feed_links',
            'feed_links_extra',
            'wlwmanifest_link',
            'print_emoji_detection_script',
            'rest_output_link_wp_head',
            'wp_shortlink_wp_head',
            'print_emoji_styles',
            'rest_output_link_header',
            'wp_shortlink_header',
            'pings_open',
            'src_version',
            'xmlrpc_enabled',
            'wp_resource_hints',
            'remove_unnecessary_headers',
            'rest_output_link_header',
            'admin_bar',
        ];
        $merged = array_diff( $all, $exclude );
        self::disable( $merged );
    }

    /**
     * Disable Easy Table of Contents debug output.
     */
    public static function disable_easy_toc_debug() : void {
        add_filter(
            'Easy_Plugins/Table_Of_Contents/Debug/Display',
            '__return_false'
        );
    }

    /**
     * Removes `ver` parameter from the script URL.
     *
     * @param string $src Script URL.
     *
     * @return string
     */
    public static function remove_src_version( $src ) {
        global $wp_version;

        $version_str        = '?ver=' . $wp_version;
        $version_str_offset = strlen( $src ) - strlen( $version_str );

        if ( substr( $src, $version_str_offset ) === $version_str ) {
            return substr( $src, 0, $version_str_offset );
        }

        return $src;
    }

    /**
     * Removes (dequeue) scripts.
     *
     * @param string $script_slug Script slug.
     */
    private static function dequeue_script( string $script_slug ) : void {
        wp_dequeue_style( $script_slug );
    }

    /**
     * Removes WP initial resource hints to browsers for pre-fetching, pre-rendering and pre-connecting to web sites.
     *
     * @return array
     */
    public static function empty_array() {
        return array();
    }

    /**
     * Removes PHP version from the response headers.
     *
     * @param array $headers Response headers array.
     *
     * @return array
     */
    public static function remove_unnecessary_headers( $headers ) {
        // Remove some headers generated by Apache.
        if ( function_exists( 'header_remove' ) ) {
            header_remove( 'X-Powered-By' ); // PHP 5.3+.
        }

        return $headers;
    }

    /**
     * Set excerpt word length.
     *
     * @param int $length Number of words.
     */
    public static function excerpt_length( $length ) {
        add_filter( 'excerpt_length', fn() => $length, 999 );
    }

    /**
     * Set excerpt more text.
     *
     * @param string $more Number of words.
     */
    public static function excerpt_more( $more, $is_link = false ) : void {
        add_filter(
            'excerpt_more',
            static function() use ( $more, $is_link ) {
                if ( $is_link ) {
                    return sprintf( ' <a href="%s" class="more-link">%s</a>', get_permalink(), $more );
                }

                return $more;
            }
        );
    }

    /**
     * Removes part of title from Archive Page title
     *
     * @return void
     */
    public static function remove_archive_title_prefix() : void {
        add_filter(
            'get_the_archive_title',
            static function( $title ) {
                return preg_replace( '/^(Archives)\: /', '', $title );
            }
        );
    }

    /**
     * Set post type archive to order by menu order.
     *
     * @param $post_type
     *
     * @return void
     */
    public static function make_cpt_order_by_menu( $post_type ) : void {
        add_action( 'pre_get_posts',
            static function( $query ) use ( $post_type ) {
                if ( $query->is_main_query() && is_post_type_archive( $post_type ) ) {
                    $query->set( 'orderby', 'menu_order' );
                    $query->set( 'order', 'ASC' );
                }
            }
        );
    }
}
