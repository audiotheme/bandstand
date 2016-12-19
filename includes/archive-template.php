<?php
/**
 * Post type archives template functions.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Display a post type archive title.
 *
 * Just a wrapper to the default post_type_archive_title for the sake of
 * consistency. This should only be used in Bandstand-specific template files.
 *
 * @since 1.0.0
 *
 * @see post_type_archive_title()
 *
 * @param string $before Optional. Content to prepend to the title. Default empty.
 * @param string $after  Optional. Content to append to the title. Default empty.
 */
function the_bandstand_archive_title( $before = '', $after = '' ) {
	$title = apply_filters( 'bandstand_archive_title', post_type_archive_title( '', false ) );

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;
	}
}

/**
 * Display a post type archive description.
 *
 * @since 1.0.0
 *
 * @param string $before Content to display before the description.
 * @param string $after Content to display after the description.
 */
function the_bandstand_archive_description( $before = '', $after = '' ) {
	if ( is_post_type_archive() ) {
		$post_type_object = get_queried_object();

		if ( $archive_id = get_bandstand_post_type_archive( $post_type_object->name ) ) {
			$archive = get_post( $archive_id );
			if ( ! empty( $archive->post_content ) ) {
				echo $before . apply_filters( 'the_content', $archive->post_content ) . $after;
			}
		}
	}

	if ( is_tax() && ! empty( get_queried_object()->description ) ) {
		echo $before . apply_filters( 'the_content', term_description() ) . $after;
	}
}

/**
 * Get archive post IDs.
 *
 * @since 1.0.0
 *
 * @return array Associative array with post types as keys and post IDs as the values.
 */
function get_bandstand_archive_ids() {
	return bandstand()->modules['archives']->get_archive_ids();
}

/**
 * Get the archive post ID for a particular post type.
 *
 * @since 1.0.0
 *
 * @param  string $post_type Optional. Post type name.
 * @return int|null
 */
function get_bandstand_post_type_archive( $post_type = null ) {
	return bandstand()->modules['archives']->get_archive_id( $post_type );
}

/**
 * Determine if the current template is a post type archive.
 *
 * @since 1.0.0
 *
 * @param  array|string $post_types Optional. A post type name or array of
 *                                  post type names. Defaults to all archives
 *                                  registered via Bandstand_PostType_Archive::add_post_type_archive().
 * @return bool
 */
function is_bandstand_post_type_archive( $post_types = array() ) {
	return bandstand()->modules['archives']->is_post_type_archive( $post_types );
}

/**
 * Determine if a post ID is for a post type archive post.
 *
 * @since 1.0.0
 *
 * @param  int $archive_id Post ID.
 * @return string|bool Post type name if true, otherwise false.
 */
function is_bandstand_post_type_archive_id( $archive_id ) {
	return bandstand()->modules['archives']->is_archive_id( $archive_id );
}

/**
 * Retrieve archive meta.
 *
 * @since 1.0.0
 *
 * @param  string $key       Optional. The meta key to retrieve. By default, returns data for all keys.
 * @param  bool   $single    Optional. Whether to return a single value.
 * @param  mixed  $default   Optional. A default value to return if the requested meta doesn't exist.
 * @param  string $post_type Optional. The post type archive to retrieve meta data for. Defaults to the current post type.
 * @return mixed  Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function get_bandstand_archive_meta( $key = '', $single = false, $default = null, $post_type = null ) {
	return bandstand()->modules['archives']->get_archive_meta( $key, $single, $default, $post_type );
}
