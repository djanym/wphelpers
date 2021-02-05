<?php

namespace Ricubai\WPHelpers;

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
                case 'rest_output_link_header': // remove Link header for rest api.
                    remove_action( 'template_redirect', 'rest_output_link_header', 11 );
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
     * Removes `ver` parametr from the script URL.
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
     * Removes unused css classes from menu items.
     */
    public static function simplify_nav_classes() {
        add_filter( 'nav_menu_css_class', '\Ricubai\WPHelpers\ConfigHelper::simplify_nav_classes', 100, 4 );
    }

    /**
     * Removes unused css classes from menu items.
     *
     * @param array $classes Array of css classes.
     *
     * @return array
     */
    public static function simplify_nav_classes_callback( $classes ) {
        foreach (
            [
                'menu-item-type-post_type',
                'menu-item-type-custom',
                'menu-item-type-post_type',
                'menu-item-object-page',
                'menu-item-home',
                'menu-item-object-custom',
                'current-menu-ancestor',
                'current_page_parent',
            ] as $class
        ) {
            $key = array_search( $class, $classes, true );
            if ( $key !== false ) {
                unset( $classes[ $key ] );
            }
        }

        return $classes;
    }

}
