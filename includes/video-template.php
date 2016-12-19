<?php
/**
 * Video template tags and functions.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Retrieve a video object.
 *
 * @since 1.0.0
 *
 * @param  mixed $post Optional. CPT slug, post ID or post object.
 * @return Bandstand_Post_Video
 */
function get_bandstand_video( $post = null ) {
	return bandstand()->post_factory->make( 'video', $post );
}

/**
 * Save a video.
 *
 * @since 1.0.0
 *
 * @param  Bandstand_Post_Video|array $video Video object or array of video data.
 * @return Bandstand_Post_Video
 */
function save_bandstand_video( $video ) {
	$repository = new Bandstand_Repository_PostRepository();
	return $repository->save( 'video', $video );
}

/**
 * Display the video URL.
 *
 * @since 1.0.0
 */
function the_bandstand_video_url() {
	echo esc_url( get_bandstand_video_url() );
}

/**
 * Retrieve a video URL.
 *
 * @since 1.0.0
 *
 * @param  mixed $post Optional. CPT slug, post ID or post object. Defaults to the current post.
 * @return string
 */
function get_bandstand_video_url( $post = null ) {
	return get_bandstand_video( $post )->get_url();
}

/**
 * Display a video.
 *
 * @since 1.0.0
 *
 * @param array $args       Optional. (width, height).
 * @param array $query_args Optional. Provider specific parameters.
 */
function the_bandstand_video_html( $args = array(), $query_args = array() ) {
	echo get_bandstand_video_html( get_the_ID(), $args, $query_args );
}

/**
 * Retrieve a video.
 *
 * @since 1.0.0
 *
 * @param  mixed $post       Optional. CPT slug, post ID or post object. Defaults to the current post.
 * @param  array $args       Optional. (width, height).
 * @param  array $query_args Optional. Provider specific parameters.
 * @return string
 */
function get_bandstand_video_html( $post = null, $args = array(), $query_args = array() ) {
	return get_bandstand_video( $post )->get_html( $args, $query_args );
}
