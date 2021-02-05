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

}
