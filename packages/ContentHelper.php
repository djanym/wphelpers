<?php
/**
 * Helpers related to WordPress content management.
 * - Cover image
 * - Quick Shortcodes: current year
 * - Pagination
 * - Contact Form 7 Google Tag Manager event
 */

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
     * @param string|array $args Array of arguments.
     *
     * @return void
     */
    public static function set_cover_image_field_source( $args ) : void {
        $args = wp_parse_args(
            $args,
            [
                'wp_featured_image_only' => false, // If true, then only featured image will be used as a cover image.
            ]
        );
        if ( $args['wp_featured_image_only'] ) {
            self::$cover_image_field_order = [ 'wp_featured_image' ];
        }
    }

    /**
     * Get cover image source URL.
     * Should be set via `set_cover_image_field_source` method.
     *
     * @return string|null
     */
    public static function get_cover_image_src( $post_id = null ) : ?string {
        $image_url = null;
        foreach ( self::$cover_image_field_order as $source_name ) {
            if ( $source_name === 'wp_featured_image' ) {
                // If no post ID provided, then check if the current page is a blog page.
                if ( ! $post_id ) {
                    if ( is_home() || is_archive() ) {
                        // Get the ID of the page set as the blog page.
                        $blog_page_id = get_option( 'page_for_posts' );

                        // Check if the blog page is set, then use it as a post ID.
                        if ( $blog_page_id ) {
                            $post_id = $blog_page_id;
                        }
                    }
                }

                $image_url = get_the_post_thumbnail_url( $post_id, 'full' );
            } else {
                $image_url = get_cf_content( $source_name, $post_id );
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

                if ( ! trim( $image ) ) {
                    // Check if the current page is a blog page.
                    if ( is_home() || is_category() || is_tag() || is_archive() ) {
                        // Get the ID of the page set as the blog page.
                        $blog_page_id = get_option( 'page_for_posts' );

                        // Check if the blog page exists and has a featured image.
                        if ( $blog_page_id ) {
                            $image = get_the_post_thumbnail( $blog_page_id, 'full', [ 'class' => 'cover-image' ] );
                        }
                    }
                }
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

    /**
     * Register a shortcode.
     * Shortcode prints current year value in YYYY format.
     *
     * @return void
     */
    public static function year_shortcode() : void {
        add_shortcode(
            'year',
            static function() {
                return current_time( 'Y' );
            }
        );
    }

    /**
     * Prints custom pagination. Base on paginate_links(), core Wordpress function.
     *
     * @param string|array $args           {
     *                                     Optional. Array or string of arguments for generating paginated links for archives.
     *                                     See pagination_links() in Wordpress Codex.
     *                                     These arguments are not listed in core function:
     *
     * @type string        $before_links   A string to appear before all links. Like wrapper. Default '<nav><ul>'.
     * @type string        $after_links    A string to append after all links. Default '</ul></nav>'.
     * @type string        $before_link    A string to appear before the link. Default '<li>'.
     * @type string        $after_link     A string to append after the link. Default '</li>'.
     * @type string        $before_current A string to append before the current link. Default '<li>'.
     * @type string        $after_current  A string to append after the current link. Default '</li>'.
     * @type string        $link_class     A class to insert for each link. Default empty.
     * @type string        $current_class  A class to append for current page. Default 'current'.
     *                                     }
     *
     * @param array|string $args
     */
    public static function paginator( $args = [] ) {
        paginate_links();
        global $wp_query, $wp_rewrite;

        // Setting up default values based on the current URL.
        $pagenum_link = html_entity_decode( get_pagenum_link() );
        $url_parts    = explode( '?', $pagenum_link );

        $total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
        $current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

        // Append the format placeholder to the base URL.
        $pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

        // URL base depends on permalink settings.
        $format = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
        $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

        $defaults = array(
            'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
            'format'             => $format, // ?page=%#% : %#% is replaced by the page number
            'total'              => $total,
            'current'            => $current,
            'aria_current'       => 'page',
            'show_all'           => false,
            'prev_next'          => true,
            'prev_text'          => __( '&laquo; Previous' ),
            'next_text'          => __( 'Next &raquo;' ),
            'end_size'           => 1,
            'mid_size'           => 2,
            'type'               => 'plain',
            'add_args'           => array(), // array of query args to add
            'add_fragment'       => '',
            'before_page_number' => '',
            'after_page_number'  => '',
            // New args
            'before_links'       => '<nav><ul>',
            'after_links'        => '</ul></nav>',
            'before_link'        => '<li>',
            'after_link'         => '</li>',
            'before_current'     => '<li>',
            'after_current'      => '</li>',
            'link_class'         => '',
            'current_class'      => 'current'
        );

        $args = wp_parse_args( $args, $defaults );

        if ( ! is_array( $args['add_args'] ) ) {
            $args['add_args'] = array();
        }

        // Merge additional query vars found in the original URL into 'add_args' array.
        if ( isset( $url_parts[1] ) ) {
            // Find the format argument.
            $format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
            $format_query = isset( $format[1] ) ? $format[1] : '';
            wp_parse_str( $format_query, $format_args );

            // Find the query args of the requested URL.
            wp_parse_str( $url_parts[1], $url_query_args );

            // Remove the format argument from the array of query arguments, to avoid overwriting custom format.
            foreach ( $format_args as $format_arg => $format_arg_value ) {
                unset( $url_query_args[ $format_arg ] );
            }

            $args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
        }

        // Who knows what else people pass in $args
        $total = (int) $args['total'];
        if ( $total < 2 ) {
            return;
        }
        $current  = (int) $args['current'];
        $end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
        if ( $end_size < 1 ) {
            $end_size = 1;
        }
        $mid_size = (int) $args['mid_size'];
        if ( $mid_size < 0 ) {
            $mid_size = 2;
        }
        $add_args   = $args['add_args'];
        $r          = '';
        $page_links = array();
        $dots       = false;

        if ( $args['prev_next'] && $current && 1 < $current ) :
            $link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
            $link = str_replace( '%#%', $current - 1, $link );
            if ( $add_args ) {
                $link = add_query_arg( $add_args, $link );
            }
            $link .= $args['add_fragment'];

            /**
             * Filters the paginated links for the given archive pages.
             *
             * @param string $link The paginated link URL.
             *
             * @since 3.0.0
             */
            $page_links[] = $args['before_link']
                            . '<a href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '" class="prev ' . $args['link_class'] . '">' . $args['prev_text'] . '</a>'
                            . $args['after_link'];
        endif;
        for ( $n = 1; $n <= $total; $n ++ ) :
            if ( $n == $current ) :
                $page_links[] = $args['before_current']
                                . "<span aria-current='" . esc_attr( $args['aria_current'] ) . "' class='" . $args['current_class'] . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</span>"
                                . $args['after_current'];;
                $dots = true;
            else :
                if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
                    $link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
                    $link = str_replace( '%#%', $n, $link );
                    if ( $add_args ) {
                        $link = add_query_arg( $add_args, $link );
                    }
                    $link .= $args['add_fragment'];

                    /** This filter is documented in wp-includes/general-template.php */
                    $page_links[] = $args['before_link']
                                    . "<a href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "' class='" . $args['link_class'] . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</a>"
                                    . $args['after_link'];
                    $dots         = true;
                elseif ( $dots && ! $args['show_all'] ) :
                    $page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';
                    $dots         = false;
                endif;
            endif;
        endfor;
        if ( $args['prev_next'] && $current && $current < $total ) :
            $link = str_replace( '%_%', $args['format'], $args['base'] );
            $link = str_replace( '%#%', $current + 1, $link );
            if ( $add_args ) {
                $link = add_query_arg( $add_args, $link );
            }
            $link .= $args['add_fragment'];

            /** This filter is documented in wp-includes/general-template.php */
            $page_links[] = $args['before_link']
                            . '<a href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '" class="next ' . $args['link_class'] . '">' . $args['next_text'] . '</a>'
                            . $args['after_link'];
        endif;

        echo $args['before_links'];
        echo implode( "\n\t", $page_links );
        echo $args['after_links'];
    }

}
