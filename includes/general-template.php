<?php
/**
 * General template tags and functions.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Load a template part into a template.
 *
 * @since 1.0.0
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function get_bandstand_template_part( $slug, $name = null ) {
	bandstand()->templates->loader->get_template_part( $slug, $name );
}

/**
 * Print the class attribute for the posts container.
 *
 * @since 1.0.0
 *
 * @param array $classes Array of HTML classes.
 */
function bandstand_posts_container_class( $classes = array() ) {
	printf(
		' class="%s"',
		esc_attr( implode( ' ', get_bandstand_posts_container_classes( $classes ) ) )
	);
}

/**
 * Retrieve classes for the posts container.
 *
 * @since 1.0.0
 *
 * @param  array|string $classes Array of HTML classes.
 * @return array
 */
function get_bandstand_posts_container_classes( $classes = array() ) {
	// Split a string.
	if ( ! empty( $classes ) && ! is_array( $classes ) ) {
		$classes = preg_split( '#\s+#', $classes );
	}

	return apply_filters( 'bandstand_posts_container_classes', $classes );
}

/**
 * Strip the protocol and trailing slash from a URL for display.
 *
 * @since 1.0.0
 *
 * @param string $url URL to simplify.
 * @return string
 */
function bandstand_simplify_url( $url ) {
	return untrailingslashit( preg_replace( '|^https?://(www\.)?|i', '', esc_url( $url ) ) );
}
