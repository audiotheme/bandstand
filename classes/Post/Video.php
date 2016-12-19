<?php
/**
 * Video post.
 *
 * @package   Bandstand\Videos
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Video post class.
 *
 * @package Bandstand\Videos
 * @since   1.0.0
 */
class Bandstand_Post_Video extends Bandstand_Post_AbstractPost {
	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const POST_TYPE = 'bandstand_video';

	/**
	 * Duration.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $duration;

	/**
	 * Video URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $video_url;

	/**
	 * Whether a duration has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_duration() {
		return ! empty( $this->duration );
	}

	/**
	 * Retrieve the duration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_duration() {
		return $this->duration;
	}

	/**
	 * Retrieve the video embed HTMl.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args       Optional. (width, height).
	 * @param  array $query_args Optional. Provider specific parameters.
	 * @return string Video HTML
	 */
	public function get_html( $args = array(), $query_args = array() ) {
		global $wp_embed;

		$html      = '';
		$video_url = $this->get_url();

		if ( ! empty( $video_url ) ) {
			// Save current embed settings and restore them after running the shortcode.
			$restore_post_id       = $wp_embed->post_ID;
			$restore_false_on_fail = $wp_embed->return_false_on_fail;
			$restore_linkifunknown = $wp_embed->linkifunknown;
			$restore_usecache      = $wp_embed->usecache;

			// Can't be sure what the embed settings are, so explicitly set them.
			$wp_embed->post_ID              = $this->ID;
			$wp_embed->return_false_on_fail = true;
			$wp_embed->linkifunknown        = false;
			$wp_embed->usecache             = true;

			$html = $wp_embed->shortcode( $args, add_query_arg( $query_args, $video_url ) );

			// Restore original embed settings.
			$wp_embed->post_ID              = $restore_post_id;
			$wp_embed->return_false_on_fail = $restore_false_on_fail;
			$wp_embed->linkifunknown        = $restore_linkifunknown;
			$wp_embed->usecache             = $restore_usecache;
		}

		if ( false !== strpos( $html, '[video' ) ) {
			$html = do_shortcode( $html );
		}

		return apply_filters( 'bandstand_video_html', $html, $this->ID, $video_url, $args, $query_args );
	}

	/**
	 * Whether a video URL has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_url() {
		return ! empty( $this->video_url );
	}

	/**
	 * Retrieve the video URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->video_url;
	}
}
