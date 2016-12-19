<?php
/**
 * Generic utility functions.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Gives a nicely formatted list of timezone strings.
 *
 * Strips the manual offsets from the default WordPress list.
 *
 * @since 1.0.0
 * @uses wp_timezone_choice()
 *
 * @param string $selected_zone Selected Zone.
 * @return string
 */
function bandstand_timezone_choice( $selected_zone = null ) {
	$selected = empty( $selected_zone ) ? get_option( 'timezone_string' ) : $selected_zone;
	$choices  = wp_timezone_choice( $selected );

	// Remove the manual offsets optgroup.
	$pos = strrpos( $choices, '<optgroup' );
	if ( false !== $pos ) {
		$choices = substr( $choices, 0, $pos );
	}

	return apply_filters( 'bandstand_timezone_dropdown', $choices, $selected );
}

/**
 * Remove a portion of an associative array, optionally replace it with something else
 * and maintain the keys.
 *
 * Can produce unexpected behavior with numeric indexes. Use array_splice() if
 * keys don't need to be preserved, although exact behavior of offset and
 * length is not duplicated.
 *
 * @since 1.0.0
 *
 * @version 1.0.0
 * @see array_splice()
 *
 * @param array  $input The input array.
 * @param int    $offset The position to start from.
 * @param int    $length Optional. The number of elements to remove. Defaults to 0.
 * @param mixed  $replacement Optional. Item(s) to replace removed elements.
 * @param string $primary Optiona. input|replacement Defaults to input. Which array should take precedence if there is a key collision.
 * @return array The modified array.
 */
function bandstand_array_asplice( $input, $offset, $length = 0, $replacement = null, $primary = 'input' ) {
	$input = (array) $input;
	$replacement = (array) $replacement;

	$start = array_slice( $input, 0, $offset, true );
	// @todo $remove = array_slice( $input, $offset, $length, true );
	$end = array_slice( $input, $offset + $length, null, true );

	// Discard elements in $replacement whose keys match keys in $input.
	if ( 'input' === $primary ) {
		$replacement = array_diff_key( $replacement, $input );
	}

	// Discard elements in $start and $end whose keys match keys in $replacement.
	// Could change the size of $input, so this is done after slicing the start and end.
	elseif ( 'replacement' === $primary ) {
		$start = array_diff_key( $start, $replacement );
		$end = array_diff_key( $end, $replacement );
	}

	// Which is faster?
	// @todo return $start + $replacement + $end;
	return array_merge( $start, $replacement, $end );
}

/**
 * Insert an element(s) after a particular value if it exists in an array.
 *
 * @since 1.0.0
 *
 * @version  1.0.0
 * @uses bandstand_array_find()
 * @uses bandstand_array_asplice()
 *
 * @param array $input The input array.
 * @param mixed $needle Value to insert new elements after.
 * @param mixed $insert The element(s) to insert.
 * @return array|bool Modified array or false if $needle couldn't be found.
 */
function bandstand_array_insert_after( $input, $needle, $insert ) {
	$input = (array) $input;
	$insert = (array) $insert;

	$position = bandstand_array_find( $needle, $input );
	if ( false === $position ) {
		return false;
	}

	return bandstand_array_asplice( $input, $position + 1, 0, $insert );
}

/**
 * Insert an element(s) after a certain key if it exists in an array.
 *
 * Use array_splice() if keys don't need to be maintained.
 *
 * @since 1.0.0
 *
 * @version 1.0.0
 * @uses bandstand_array_key_find()
 * @uses bandstand_array_asplice()
 *
 * @param array $input The input array.
 * @param mixed $needle Value to insert new elements after.
 * @param mixed $insert The element(s) to insert.
 * @return array|bool Modified array or false if $needle couldn't be found.
 */
function bandstand_array_insert_after_key( $input, $needle, $insert ) {
	$input = (array) $input;
	$insert = (array) $insert;

	$position = bandstand_array_key_find( $needle, $input );
	if ( false === $position ) {
		return false;
	}

	return bandstand_array_asplice( $input, $position + 1, 0, $insert );
}

/**
 * Find the position (not index) of a value in an array.
 *
 * @since 1.0.0
 *
 * @version 1.0.0
 * @see array_search()
 * @uses bandstand_array_key_find()
 *
 * @param mixed $needle The value to search for.
 * @param array $haystack The array to search.
 * @param bool  $strict Whether to search for identical (types) values.
 * @return int|bool Position of the first matching element or false if not found.
 */
function bandstand_array_find( $needle, $haystack, $strict = false ) {
	if ( ! is_array( $haystack ) ) {
		return false;
	}

	$key = array_search( $needle, $haystack, $strict );

	return ( $key ) ? bandstand_array_key_find( $key, $haystack ) : false;
}

/**
 * Find the position (not index) of a key in an array.
 *
 * @since 1.0.0
 *
 * @version 1.0.0
 * @see array_key_exists()
 *
 * @param string|int $key The key to search for.
 * @param array      $search The array to search.
 * @return int|bool Position of the key or false if not found.
 */
function bandstand_array_key_find( $key, $search ) {
	$key = ( is_int( $key ) ) ? $key : (string) $key;

	if ( ! is_array( $search ) ) {
		return false;
	}

	$keys = array_keys( $search );

	return array_search( $key, $keys, true );
}

/**
 * Return a base64 encoded SVG icon for use as a data URI.
 *
 * @since 1.0.0
 *
 * @param string $path Path to SVG icon.
 * @return string
 */
function bandstand_encode_svg( $path ) {
	if ( ! path_is_absolute( $path ) ) {
		$path = bandstand()->get_path( $path );
	}

	if ( ! file_exists( $path ) || 'svg' !== pathinfo( $path, PATHINFO_EXTENSION ) ) {
		return '';
	}

	return 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( $path ) );
}

/**
 * Encode the path portion of a URL.
 *
 * Spaces in directory or filenames are stripped by esc_url() and can cause
 * issues when requesting a URL programmatically. This method encodes spaces
 * and other characters.
 *
 * @since 1.0.0
 *
 * @param string $url A URL.
 * @return string
 */
function bandstand_encode_url_path( $url ) {
	$parts = parse_url( $url );

	$return  = isset( $parts['scheme'] ) ? $parts['scheme'] . '://' : '';
	$return .= isset( $parts['host'] ) ? $parts['host'] : '';
	$return .= isset( $parts['port'] ) ? ':' . $parts['port'] : '';
	$user = isset( $parts['user'] ) ? $parts['user'] : '';
	$pass = isset( $parts['pass'] ) ? ':' . $parts['pass']  : '';
	$return .= ( $user || $pass ) ? "$pass@" : '';

	if ( isset( $parts['path'] ) ) {
		$path = implode( '/', array_map( 'rawurlencode', explode( '/', $parts['path'] ) ) );
		$return .= $path;
	}

	$return .= isset( $parts['query'] ) ? '?' . $parts['query'] : '';
	$return .= isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '';

	return $return;
}

/**
 * Return key value pairs with argument and operation separators.
 *
 * @since 1.0.0
 *
 * @param array  $data Array of properties.
 * @param string $arg_separator Separator between arguments.
 * @param string $value_separator Separator between keys and values.
 * @return array string
 */
function bandstand_build_query( $data, $arg_separator = '|', $value_separator = ':' ) {
	$output = http_build_query( $data, null, $arg_separator );
	return str_replace( '=', $value_separator, $output );
}

/**
 * Make an internal REST request.
 *
 * @since 1.0.0
 *
 * @link https://gist.github.com/joehoyle/e7321570525af6daeae2
 *
 * @param  WP_REST_Request|string $request A request instance or a request method.
 * @param  string                 $path    Optional. Path if not specified in the request.
 * @param  array                  $data    Optional. Data for the request.
 * @return WP_Error|mixed
 */
function bandstand_rest_request( $request, $path = null, $data = array() ) {
	if ( ! ( $request instanceof WP_REST_Request ) ) {
		$request = new WP_REST_Request( $request, $path );

		foreach ( $data as $k => $v ) {
			$request->set_param( $k, $v );
		}
	}

	$result = rest_do_request( $request );

	if ( $result->is_error() ) {
		return $result->as_error();
	}

	$result = rest_get_server()->response_to_data( $result, ! empty( $data['_embed'] ) );

	return $result;
}

/**
 * Generate a UUID using the v4 algorithm.
 *
 * @since 1.0.0
 *
 * @link http://php.net/manual/en/function.uniqid.php#94959
 * @link https://github.com/rmccue/realguids
 *
 * @return string Generated UUID.
 */
function bandstand_generate_uuid_v4() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

		// 32 bits for "time_low"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

		// 16 bits for "time_mid"
		mt_rand( 0, 0xffff ),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand( 0, 0x0fff ) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand( 0, 0x3fff ) | 0x8000,

		// 48 bits for "node"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}
