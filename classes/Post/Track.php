<?php
/**
 * Track post.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Track post class.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_Post_Track extends Bandstand_Post_AbstractPost {
	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const POST_TYPE = 'bandstand_track';

	/**
	 * Artist.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $artist = '';

	/**
	 * Download URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $download_url = '';

	/**
	 * Formatted duration.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $duration = '';

	/**
	 * Purchase URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $purchase_url = '';

	/**
	 * Stream URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $stream_url = '';

	/**
	 * Track number.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $track_number = '';

	/**
	 * Whether an artist exists.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_artist() {
		return ! empty( $this->artist );
	}

	/**
	 * Retrieve the artist.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_artist() {
		return $this->artist;
	}

	/**
	 * Whether the track can be downloaded.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_downloadable() {
		return ! empty( $this->download_url );
	}

	/**
	 * Retrieve the download URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_download_url() {
		return $this->download_url;
	}

	/**
	 * Whether the duration has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_duration() {
		return ! empty( $this->duration );
	}

	/**
	 * Retrieve the track duration.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_duration() {
		return $this->duration;
	}

	/**
	 * Whether a purchase URL exists.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_purchase_url() {
		return ! empty( $this->purchase_url );
	}

	/**
	 * Retrieve the purchase URL.
	 *
	 * @since 1.0.0
	 *
	 * @todo Allow for multiple URLs.
	 *
	 * @return string
	 */
	public function get_purchase_url() {
		return $this->purchase_url;
	}

	/**
	 * Whether the track can be streamed.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_streamable() {
		return ! empty( $this->stream_url );
	}

	/**
	 * Retrieve the stream URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_stream_url() {
		return $this->stream_url;
	}

	/**
	 * Retrieve the record the track belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return Bandstand_Post_Record
	 */
	public function get_record() {
		if ( $this->has_post() && $this->get_post()->post_parent ) {
			return get_bandstand_record( $this->get_post()->post_parent );
		}

		return null;
	}

	/**
	 * Retrieve the track number.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_track_number() {
		return $this->track_number;
	}
}
