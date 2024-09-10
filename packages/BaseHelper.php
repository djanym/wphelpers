<?php

namespace Ricubai\WPHelpers;

class BaseHelper {
    public static function enable_mega_menu() {
//        add_filter( 'wp_nav_menu_objects', '\Ricubai\WPHelpers\BaseHelper::megamenu_menu_objects', 10, 2 );
        add_filter( 'wp_nav_menu_objects', [ __CLASS__, 'megamenu_menu_objects' ], 10, 2 );
//        add_action( 'acf/init', '\Ricubai\WPHelpers\BaseHelper::megamenu_acf', 100 );
        add_action( 'acf/init', [ __CLASS__, 'megamenu_acf' ], 100 );
    }

    /**
     * Implements megamenu classes. Works with ACF for menu items.
     *
     * @param array $items The menu items, sorted by each menu item's menu order.
     *
     * @return array
     */
    public static function megamenu_menu_objects( $items ) : array {
        foreach ( $items as &$item ) {
            $megamenu_option = get_field( 'is_megamenu', $item );
            if ( $megamenu_option === 'megamenu_container' ) {
                $item->classes[] = 'has-mega-menu';

                $columns_number = get_field( 'megamenu_columns_number', $item );
                if ( $columns_number ) {
                    $item->classes[] = 'columns-' . $columns_number;
                }
            }
//            } elseif ( $megamenu_option === 'megamenu_column' ) {
//                $item->classes[] = 'nav-column';

            $hide_column_title_option = get_field( 'hide_column_title', $item );
            if ( $hide_column_title_option ) {
                $item->classes[] = 'hide-mm-section-title';
            }
//            }
        }

        return $items;
    }

    public static function megamenu_acf() {
        if ( function_exists( 'acf_add_local_field_group' ) ):
            acf_add_local_field_group( array(
                'key'                   => 'group_theme_megamenu',
                'title'                 => 'MegaMenu',
                'fields'                => array(
                    [
                        'key'               => 'field_is_megamenu',
                        'label'             => 'Is it a Megamenu Item?',
                        'name'              => 'is_megamenu',
                        'type'              => 'radio',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => [
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ],
                        'choices'           => [
                            'no'                 => 'No',
                            'megamenu_container' => 'Megamenu Container',
//                            'megamenu_column'    => 'Megamenu Column',
                        ],
                        'allow_null'        => 0,
                        'other_choice'      => 0,
                        'default_value'     => 'no',
                        'layout'            => 'vertical',
                        'return_format'     => 'value',
                        'save_other_choice' => 0,
                    ],
                    // Number of columns if megamenu is enabled
                    [
                        'key'               => 'field_megamenu_columns',
                        'label'             => 'Number of Columns',
                        'name'              => 'megamenu_columns_number',
                        'type'              => 'number',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => [
                            [
                                [
                                    'field'    => 'field_is_megamenu',
                                    'operator' => '==',
                                    'value'    => 'megamenu_container',
                                ],
                            ],
                        ],
                        'wrapper'           => [
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ],
                        'default_value'     => 2,
                        'placeholder'       => '',
                        'prepend'           => '',
                        'append'            => '',
                        'min'               => 2,
                        'max'               => 6,
                        'step'              => 1,
                    ],
                    [
                        'key'               => 'field_hide_column_title',
                        'label'             => 'Hide Column Title',
                        'name'              => 'hide_column_title',
                        'type'              => 'true_false',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => [
                            [
                                [
                                    'field'    => 'field_is_megamenu',
                                    'operator' => '==',
                                    'value'    => 'megamenu_column',
                                ],
                            ],
                        ],
                        'wrapper'           => [
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ],
                        'message'           => '',
                        'default_value'     => 0,
                        'ui'                => 1,
                        'ui_on_text'        => '',
                        'ui_off_text'       => '',
                    ],
                ),
                'location'              => array(
                    array(
                        array(
                            'param'    => 'nav_menu_item',
                            'operator' => '==',
                            'value'    => 'all',
                        ),
                    ),
                ),
                'menu_order'            => 0,
                'position'              => 'normal',
                'style'                 => 'default',
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen'        => '',
                'active'                => true,
                'description'           => '',
                'modified'              => 1564259301,
            ) );
        endif;
    }

    /**
     * Enables option in Admin Dashboard -> Appearance -> Menus to show/hide menu items based on user logged in status.
     *
     * @return void
     */
    public static function enable_show_hide_menu_option() {
        add_filter( 'wp_nav_menu_objects', [ __CLASS__, 'showhide_menu_objects' ], 10, 2 );
        add_action( 'acf/init', [ __CLASS__, 'showhide_acf' ], 100 );
    }

    /**
     * Implements show/hide functionality for menu items. Works with ACF for menu items.
     *
     * @param array $items The menu items, sorted by each menu item's menu order.
     *
     * @return array
     */
    public static function showhide_menu_objects( $items ) : array {
        foreach ( $items as &$item ) {
            $megamenu_option = get_field( 'showhide_switch', $item );
            if ( $megamenu_option === 'show' ) {
                if ( ! is_user_logged_in() ) {
                    $item = null;
                }
            } elseif ( $megamenu_option === 'hide' ) {
                if ( is_user_logged_in() ) {
                    $item = null;
                }
            }
        }

        return $items;
    }

    /**
     * Adds `show/hide` ACF field for menu items.
     *
     * @return void
     */
    public static function showhide_acf() {
        if ( function_exists( 'acf_add_local_field_group' ) ):
            acf_add_local_field_group( array(
                'key'                   => 'group_theme_showhide',
                'title'                 => 'Show/Hide Menu Item',
                'fields'                => array(
                    [
                        'key'               => 'field_showhide_switch',
                        'label'             => 'Show/Hide for Logged In Users Only',
                        'name'              => 'showhide_switch',
                        'type'              => 'radio',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => [
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ],
                        'choices'           => [
                            ''     => 'Always Show',
                            'show' => 'Show For Logged In Users Only',
                            'hide' => 'Hide For Logged In Users Only',
                        ],
                        'allow_null'        => 1,
                        'other_choice'      => 0,
                        'default_value'     => 'no',
                        'layout'            => 'vertical',
                        'return_format'     => 'value',
                        'save_other_choice' => 0,
                    ],
                ),
                'location'              => array(
                    array(
                        array(
                            'param'    => 'nav_menu_item',
                            'operator' => '==',
                            'value'    => 'all',
                        ),
                    ),
                ),
                'menu_order'            => 0,
                'position'              => 'normal',
                'style'                 => 'default',
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen'        => '',
                'active'                => true,
                'description'           => '',
                'modified'              => 1564259301,
            ) );
        endif;
    }

}
