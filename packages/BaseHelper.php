<?php

namespace Ricubai\WPHelpers;

class BaseHelper {
    public static function enable_mega_menu() {
        add_filter( 'wp_nav_menu_objects', '\Ricubai\WPHelpers\BaseHelper::megamenu_menu_objects', 10, 2 );
        add_action( 'acf/init', '\Ricubai\WPHelpers\BaseHelper::megamenu_acf', 100 );
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
                $item->classes[] = 'nav-columns';
            } elseif ( $megamenu_option === 'megamenu_column' ) {
                $item->classes[] = 'nav-column';
            }
        }

        return $items;
    }

    public static function megamenu_acf() {
        if ( function_exists( 'acf_add_local_field_group' ) ):
            acf_add_local_field_group( array(
                'key'                   => 'group_megamenu',
                'title'                 => 'MegaMenu',
                'fields'                => array(
                    array(
                        'key'               => 'field_is_megamenu',
                        'label'             => 'Is it a Megamenu Item?',
                        'name'              => 'is_megamenu',
                        'type'              => 'radio',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'choices'           => array(
                            'no'                 => 'No',
                            'megamenu_container' => 'Megamenu Container',
                            'megamenu_column'    => 'Megamenu Column',
                        ),
                        'allow_null'        => 0,
                        'other_choice'      => 0,
                        'default_value'     => 'no',
                        'layout'            => 'vertical',
                        'return_format'     => 'value',
                        'save_other_choice' => 0,
                    ),
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
