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

}
