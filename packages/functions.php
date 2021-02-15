<?php

if ( ! function_exists( 'echo_if' ) ) :
    /**
     * Helper for printing some string if a variable equals to a value.
     *
     * @param mixed  $var   Variable to compare.
     * @param mixed  $value Value to compare with variable.
     * @param string $print Output string in case variable equals passed value.
     */
    function echo_if( $var, $value, $print ) {
        echo $var === $value ? wp_kses_post( $print ) : '';
    }
endif;

if ( ! function_exists( 'echo_if_else' ) ) :
    /**
     * Helper for printing some string if a variable equals to a value, otherwise print else.
     *
     * @param mixed $var        Variable to compare.
     * @param mixed $value      Value to compare with variable.
     * @param mixed $print_if   Outputs string in case variable equals passed value.
     * @param mixed $print_else Outputs string in case variable does not equals passed value.
     */
    function echo_if_else( $var, $value, $print_if, $print_else ) {
        echo $var === $value ? wp_kses_post( $print_if ) : wp_kses_post( $print_else );
    }
endif;
