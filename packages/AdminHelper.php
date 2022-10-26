<?php

namespace Ricubai\WPHelpers;

class AdminHelper {
    public static function globalContentPage() : void {
        if ( function_exists( 'acf_add_options_page' ) ) {
            acf_add_options_page(
                array(
                    'page_title' => 'Global Content',
                    'menu_title' => 'Global Content',
                    'menu_slug'  => 'global-content',
                    'capability' => 'edit_posts',
                    'position'   => 4.1,
                    'icon_url'   => 'dashicons-hammer',
                )
            );
            add_action( 'admin_bar_menu', '\Ricubai\WPHelpers\AdminHelper::addToolbarItems', 100 );
        }
    }

    /**
     * Adds theme options to admin bar.
     *
     * @param WP_Admin_Bar $admin_bar Default admin bar object.
     */
    public static function addToolbarItems( $admin_bar ) {
        $admin_bar->add_menu(
            [
                'id'    => 'global-content',
                'title' => sprintf(
                    '<span class="ab-icon dashicons dashicons-hammer"></span><span class="ab-label">%s</span>',
                    'Global Content'
                ),
                'href'  => admin_url( 'admin.php?page=global-content' ),
                'meta'  => [
                    'title' => 'Global Content',
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
