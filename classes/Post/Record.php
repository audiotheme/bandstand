<?php
/**
 * Record post.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Record post class.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_Post_Record extends Bandstand_Post_AbstractPost {
	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const POST_TYPE = 'bandstand_record';

	/**
	 * Artist.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $artist = '';

	/**
	 * Catalog number.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $catalog_number = '';

	/**
	 * Record label.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $label = '';

	/**
	 * Record links.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $record_links = '';

	/**
	 * Release date.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $release_date = '';

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
	 * Whether the record has a catalog number.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_catalog_number() {
		return ! empty( $this->catalog_number );
	}

	/**
	 * Retrieve the catalog number.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_catalog_number() {
		return $this->catalog_number;
	}

	/**
	 * Whether a genre exists.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|int|array $genre Optional. Genre name, ID or slug, or an array.
	 * @return boolean
	 */
	public function has_genre( $genre = null ) {
		return has_term( $genre, 'bandstand_genre', $this->ID );
	}

	/**
	 * Retrieve the genre.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_genres() {
		return get_the_terms( $this->ID, 'bandstand_genre' );
	}

	/**
	 * Whether the record has a label.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_label() {
		return ! empty( $this->label );
	}

	/**
	 * Retrieve the record label.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Whether the record has links.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_links() {
		return ! empty( $this->record_links );
	}

	/**
	 * Retrieve the links.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_links() {
		$links = array_filter( (array) $this->record_links );
		return apply_filters( 'bandstand_record_links', $links, $this );
	}

	/**
	 * Whether a release date exists.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_release_date() {
		return ! empty( $this->release_date );
	}

	/**
	 * Retrieve the release date.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $format Date format.
	 * @param  array  $args {
	 *     Optional. Array of arguments.
	 *
	 *     @type boolean $translate Whether to translate the date. Defaults to true.
	 * }
	 * @return string
	 */
	public function get_release_date( $format = '', $args = array() ) {
		$args = wp_parse_args( $args, array(
			'translate' => true,
		) );

		if ( empty( $this->release_date ) ) {
			return '';
		}

		$release_date = preg_replace( '/([0-9]{4})-00-00/', '$1', $this->release_date );

		// Handle the case where only the release year is available.
		if ( strlen( $release_date ) === 4 ) {
			$format       = 'Y';
			$release_date = sprintf( '%s-01-01', $release_date );
		}

		if ( empty( $format ) ) {
			$format = get_option( 'date_format' );
		}

		$timestamp = strtotime( $release_date );

		if ( $args['translate'] ) {
			$result = date_i18n( $format, $timestamp );
		} else {
			$result = date( $format, $timestamp );
		}

		return $result;
	}

	/**
	 * Retrieve the number of tracks.
	 *
	 * - Get from post meta.
	 * - If it doesn't exist, need to query tracks and count them.
	 * - Then cache the value in post meta.
	 *
	 * This should be updated whenever a record or track is saved.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_track_count() {
		$count = null;

		if ( $this->has_post() ) {
			$count = get_post_meta( $this->ID, 'bandstand_track_count', true );

			if ( null === $count ) {
				$count = $this->update_track_count();
			}
		}

		return absint( $count );
	}

	/**
	 * Retrieve the tracks.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args Optional.
	 * @return array Array of track objects.
	 */
	public function get_tracks( $args = array() ) {
		if ( ! $this->has_post() ) {
			return $this->tracks;
		}

		$args = wp_parse_args( $args, array(
			'is_streamable' => false,
		) );

		$query = new WP_Query( array(
			'post_type'      => 'bandstand_track',
			'post_status'    => empty( $args['post_status'] ) ? 'publish' : $args['post_status'],
			'post_parent'    => absint( $this->ID ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		) );

		$tracks = array();
		foreach ( $query->posts as $post ) {
			if ( $args['is_streamable'] && empty( $post->bandstand_stream_url ) ) {
				continue;
			}

			$tracks[] = get_bandstand_track( $post );
		}

		return $tracks;
	}

	/**
	 * Update the number of tracks associated with the record.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $count Optional. Number of tracks associated with the record.
	 * @return int
	 */
	public function update_track_count( $count = null ) {
		global $wpdb;

		if ( null === $count ) {
			$sql = $wpdb->prepare(
				"SELECT count( * )
				FROM $wpdb->posts
				WHERE
					post_type = 'bandstand_track' AND
					post_parent = %d AND
					post_status = 'publish'",
				$this->ID
			);

			$count = absint( $wpdb->get_var( $sql ) );
		}

		update_post_meta( $this->ID, 'bandstand_track_count', $count );

		return $count;
	}
}
