<?php
/**
 * Discography template functions.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Retrieve a record.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug. Defaults to the current post.
 * @return Bandstand_Post_Record
 */
function get_bandstand_record( $post = null ) {
	return bandstand()->post_factory->make( 'record', $post );
}

/**
 * Save a record.
 *
 * @since 1.0.0
 *
 * @param  Bandstand_Post_Record|array $record Record object or array of record data.
 * @return Bandstand_Post_Record
 */
function save_bandstand_record( $record ) {
	$repository = new Bandstand_Repository_PostRepository();
	return $repository->save( 'record', $record );
}

/**
 * Retrieve a track.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug. Defaults to the current post.
 * @return Bandstand_Post_Track
 */
function get_bandstand_track( $post = null ) {
	return bandstand()->post_factory->make( 'track', $post );
}

/**
 * Save a track.
 *
 * @since 1.0.0
 *
 * @param  Bandstand_Post_Track|array $track Track object or array of track data.
 * @return Bandstand_Post_Track
 */
function save_bandstand_track( $track ) {
	$repository = new Bandstand_Repository_PostRepository();
	return $repository->save( 'track', $track );
}

/**
 * Display the current record's title.
 *
 * @since 1.0.0
 *
 * @param string $before Optional. Content to prepend to the title.
 * @param string $after  Optional. Content to append to the title.
 */
function the_bandstand_record_title( $before = '', $after = '' ) {
	$post = get_post();

	if ( 'bandstand_track' === get_post_type( $post ) ) {
		$title = get_the_title( $post->post_parent );
	} else {
		$title = get_the_title( $post->ID );
	}

	echo $before . $title . $after;
}

/**
 * Display the current record's artist.
 *
 * @since 1.0.0
 *
 * @param string $before Optional. Content to prepend to the artist.
 * @param string $after  Optional. Content to append to the artist.
 */
function the_bandstand_record_artist( $before = '', $after = '' ) {
	$artist = get_bandstand_record()->get_artist();

	if ( empty( $artist ) ) {
		return;
	}

	echo $before . $artist . $after;
}

/**
 * Display the current record's genres as a list with specified format.
 *
 * @since 1.0.0
 *
 * @todo Flesh this out with a few more arguments.
 *
 * @param  string $before    Optional. Before list.
 * @param  string $separator Optional. Separate items using this.
 * @param  string $after     Optional. After list.
 * @return string|WP_Error   A list of terms on success, false if there are no terms, WP_Error on failure.
 */
function the_bandstand_record_genres( $before = '', $separator = '', $after ) {
	/*
	$args = wp_parse_args( $args, array(
		'before'      => '',
		'after'       => '',
		'before_item' => '',
		'after_item'  => '',
		'separator'   => '',
	) );
	*/

	$record = get_bandstand_record();

	if ( ! $record->has_genre() ) {
		return false;
	}

	$terms = $record->get_genres();

	if ( is_wp_error( $terms ) ) {
		return $terms;
	}

	$genres = wp_list_pluck( $terms, 'name' );

	echo $before . implode( $separator, $genres ) . $after;
}
