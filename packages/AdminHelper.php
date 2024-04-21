<?php

namespace Ricubai\WPHelpers;

class AdminHelper {
    /**
     * Adds global content page to admin.
     *
     * @param array $args Check acf_add_options_page() for possible arguments.
     *
     * @return void
     */
    public static function globalContentPage( $args = [] ) : void {
        if ( function_exists( 'acf_add_options_page' ) ) {
            $args = wp_parse_args(
                $args,
                [
                    'page_title' => 'Global Content',
                    'menu_title' => 'Global Content',
                    'menu_slug'  => 'global-content',
                    'capability' => 'edit_posts',
                    'position'   => 4.1,
                    'icon_url'   => 'dashicons-hammer',
                ]
            );
            acf_add_options_page( $args );
            add_action( 'admin_bar_menu', function( $admin_bar ) use ( $args ) {
                self::addToolbarItems( $admin_bar, $args );
            }, 100 );
        }
    }

    /**
     * Adds theme options to admin bar.
     *
     * @param WP_Admin_Bar $admin_bar Default admin bar object.
     */
    public static function addToolbarItems( $admin_bar, $args ) {
        $admin_bar->add_menu(
            [
                'id'    => $args['menu_slug'],
                'title' => sprintf(
                    '<span class="ab-icon dashicons %s"></span><span class="ab-label">%s</span>',
                    $args['icon_url'],
                    $args['menu_title']
                ),
                'href'  => admin_url( 'admin.php?page=' . $args['menu_slug'] ),
                'meta'  => [
                    'title' => $args['page_title'],
                ],
            ]
        );
    }

    /**
     * Hides the featured image field on a page that was set up as a front page.
     */
    public static function hide_frontpage_featured_image() : void {
        add_action(
            'admin_init',
            static function() {
                $post_id = $_GET['post'] ?? ( $_POST['post_ID'] ?? false );
                if ( ! isset( $post_id ) ) {
                    return;
                }

                $frontpage_id = get_option( 'page_on_front' );

                if ( $post_id === $frontpage_id ) {
                    remove_post_type_support( 'page', 'thumbnail' );
                }
            }
        );
    }

    /**
     * Remove featured image support for pages.
     *
     * @return void
     */
    public static function hide_page_cpt_featured_image() : void {
        add_action(
            'init',
            static function() {
                remove_post_type_support( 'page', 'thumbnail' );
            }
        );
    }

    /**
     * Hides the editor field on a page that was set up as a front page.
     */
    public static function hide_frontpage_editor() : void {
        add_action(
            'admin_init',
            static function() {
                $post_id = $_GET['post'] ?? ( $_POST['post_ID'] ?? false );
                if ( ! isset( $post_id ) ) {
                    return;
                }

                $frontpage_id = get_option( 'page_on_front' );

                if ( $post_id === $frontpage_id ) {
                    remove_post_type_support( 'page', 'editor' );
                }
            }
        );
    }

    /**
     * Hides the editor field on a page that was set up as a blog page.
     */
    public static function hide_blogpage_editor() : void {
        add_action(
            'admin_init',
            function() {
                $post_id = $_GET['post'] ?? ( $_POST['post_ID'] ?? false );
                if ( ! isset( $post_id ) ) {
                    return;
                }

                $blogpage_id = get_option( 'page_for_posts' );

                // Disable content editor for blog archive page.
                if ( $post_id === $blogpage_id ) {
                    remove_post_type_support( 'page', 'editor' );
                }
            }
        );
    }

    /**
     * Hides the editor field on a page.
     *
     * @param array $args Arguments can be `template`: template name. Like page-contact.php.
     *
     * @return void
     */
    public static function hide_page_editor( array $args = [] ) : void {
        $args = wp_parse_args( $args, [
            'template' => null,
        ] );
        add_action(
            'admin_init',
            function() use ( $args ) {
                $post_id = $_GET['post'] ?? ( $_POST['post_ID'] ?? false );
                if ( ! isset( $post_id ) ) {
                    return;
                }

                // Disable content editor by page template.
                if ( $args['template'] ) {
                    $template_file = get_post_meta( $post_id, '_wp_page_template', true );

                    if ( $template_file === $args['template'] ) {
                        remove_post_type_support( 'page', 'editor' );
                    }
                }
            }
        );
    }

}
