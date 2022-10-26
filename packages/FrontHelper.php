<?php

namespace Ricubai\WPHelpers;

class FrontHelper {
	public static function body_class( $class ) : void {
		add_filter(
			'body_class',
			static function( $classes ) use ( $class ) {
				$classes[] = $class;

				return $classes;
			},
			10,
			2
		);
	}

	public static function get_category_tree( $args = '' ) {
		$defaults = array(
			'taxonomy' => 'category',
		);

		$args = wp_parse_args( $args, $defaults );

		return self::get_term_tree( $args );
	}

	public static function get_term_tree( $args = '' ) {
		$defaults = array(
			'hide_empty'   => true,
			'echo'         => false,
			'hierarchical' => 1,
			'show_count'   => false,
			'parent'       => 0,
			'orderby'      => 'name',
			'order'        => 'ASC',
			'tree'         => [],
		);

		$args = wp_parse_args( $args, $defaults );

		$cats = get_terms( $args );

		if ( $cats ) {
			foreach ( $cats as &$cat ) {
				$args['parent'] = $cat->term_id;
				$cat->childs  = self::get_term_tree( $args );
			}
		}

		return $cats;
	}

}
