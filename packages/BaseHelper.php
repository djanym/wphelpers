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

				$hide_column_title_option = get_field( 'hide_column_title', $item );
				if ( $hide_column_title_option ) {
					$item->classes[] = 'hide-column-title';
				}
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
							'megamenu_column'    => 'Megamenu Column',
						],
						'allow_null'        => 0,
						'other_choice'      => 0,
						'default_value'     => 'no',
						'layout'            => 'vertical',
						'return_format'     => 'value',
						'save_other_choice' => 0,
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
	 *
	 */
	public static function hide_frontpage_featured_image() : void {
		add_action( 'admin_init', '\Ricubai\WPHelpers\BaseHelper::hide_frontpage_featured_image_callback' );
	}

	/**
	 *
	 */
	public static function hide_frontpage_featured_image_callback() : void {
		$post_id = $_GET['post'] ?? ( $_POST['post_ID'] ?? false );
		if ( ! isset( $post_id ) ) {
			return;
		}

		$frontpage_id = get_option( 'page_on_front' );

		if ( $post_id === $frontpage_id ) {
			remove_post_type_support( 'page', 'thumbnail' );
		}
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
	 *
	 */
	public static function hide_blogpage_editor() : void {
		add_action( 'admin_init', '\Ricubai\WPHelpers\BaseHelper::hide_blogpage_editor_callback' );
	}

	/**
	 *
	 */
	public static function hide_blogpage_editor_callback() : void {
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

}
