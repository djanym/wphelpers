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
                default:
                    break;
            }
        }

        return;

// ?
        remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );

        add_filter( 'show_admin_bar', '__return_false' );
//remove_filter( 'the_content', 'wptexturize' );
//remove_filter( 'the_content', 'wpautop' );
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

}
