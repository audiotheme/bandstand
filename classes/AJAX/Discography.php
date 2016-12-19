<?php
/**
 * Discography AJAX actions.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Discography AJAX actions class.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_AJAX_Discography {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_bandstand_ajax_update_tracklist_autonumber', array( $this, 'update_tracklist_autonumber' ) );
		add_action( 'wp_ajax_bandstand_ajax_get_playlist_track',          array( $this, 'get_playlist_track' ) );
		add_action( 'wp_ajax_bandstand_ajax_get_playlist_tracks',         array( $this, 'get_playlist_tracks' ) );
		add_action( 'wp_ajax_bandstand_ajax_get_playlist_records',        array( $this, 'get_playlist_records' ) );
	}

	/**
	 * Update the autonumber setting for a record tracklist.
	 *
	 * @since 1.0.0
	 */
	public function update_tracklist_autonumber() {
		$response       = array();
		$record_id      = absint( $_POST['record_id'] );
		$is_valid_nonce = isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'update-autonumber_' . $record_id );

		if ( ! $is_valid_nonce ) {
			$response['message'] = esc_html__( 'Unauthorized request.', 'bandstand' );
			wp_send_json_error( $response );
		}

		$value = isset( $_POST['autonumber'] ) && '1' === $_POST['autonumber'] ? 'yes' : 'no';
		update_post_meta( $record_id, 'bandstand_autonumber_tracklist', $value );

		wp_send_json_success();
	}

	/**
	 * Retrieve a track for use in Cue.
	 *
	 * @since 1.0.0
	 */
	public function get_playlist_track() {
		wp_send_json_success( get_cue_playlist_track( absint( $_POST['post_id'] ) ) );
	}

	/**
	 * Retrieve a collection of tracks for use in Cue.
	 *
	 * @since 1.0.0
	 */
	public function get_playlist_tracks() {
		$posts = get_posts( array(
			'post_type'      => 'bandstand',
			'post__in'       => array_map( 'absint', array_filter( (array) $_POST['post__in'] ) ),
			'posts_per_page' => -1,
		) );

		$tracks = array();
		foreach ( $posts as $post ) {
			$tracks[] = $this->get_bandstand_playlist_track( $post );
		}

		wp_send_json_success( $tracks );
	}

	/**
	 * Retrieve a list of records and their corresponding tracks for use in Cue.
	 *
	 * @since 1.0.0
	 */
	public function get_playlist_records() {
		global $wpdb;

		$data = array();
		$page = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
		$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 2;

		$records = new WP_Query( array(
			'post_type'      => 'bandstand_record',
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'paged'          => $page,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		if ( $records->have_posts() ) {
			foreach ( $records->posts as $record ) {
				$record = get_bandstand_record( $record );

				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $record->ID ), array( 120, 120 ) );

				$data[ $record->ID ] = array(
					'id'        => $record->ID,
					'title'     => $record->post_title,
					'artist'    => $record->get_artist(),
					'release'   => $record->get_release_date( 'Y' ),
					'thumbnail' => $image[0],
					'tracks'    => array(),
				);
			}

			$tracks = $wpdb->get_results(
				"SELECT p.ID, p.post_title, p2.ID AS record_id
				FROM $wpdb->posts p
				INNER JOIN $wpdb->posts p2 ON p.post_parent = p2.ID
				WHERE p.post_type = 'bandstand' AND p.post_status = 'publish'
				ORDER BY p.menu_order ASC"
			);

			if ( $tracks ) {
				foreach ( $tracks as $track ) {
					if ( ! isset( $data[ $track->record_id ] ) ) {
						continue;
					}

					$data[ $track->record_id ]['tracks'][] = array(
						'id'    => $track->ID,
						'title' => $track->post_title,
					);
				}
			}

			// Remove records that don't have any tracks.
			foreach ( $data as $key => $item ) {
				if ( empty( $item['tracks'] ) ) {
					unset( $data[ $key ] );
				}
			}
		}

		$send['maxNumPages'] = $records->max_num_pages;
		$send['records'] = array_values( $data );

		wp_send_json_success( $send );
	}

	/**
	 * Convert a track into the format expected by the Cue plugin.
	 *
	 * @since 1.0.0
	 *
	 * @todo Update this to use the post models.
	 *
	 * @param int|WP_Post $post Post object or ID.
	 * @return object Track object expected by Cue.
	 */
	protected function get_bandstand_playlist_track( $post = 0 ) {
		$track = get_bandstand_track( $post );

		$result           = new stdClass;
		$result->id       = $track->ID;
		$result->artist   = $track->get_artist();
		$result->audioUrl = $track->get_stream_url();
		$result->title    = get_the_title( $track->ID );

		if ( $thumbnail_id = get_post_thumbnail_id( $track->ID ) ) {
			$size  = apply_filters( 'cue_artwork_size', array( 300, 300 ) );
			$image = image_downsize( $thumbnail_id, $size );

			$result->artworkUrl = $image[0];
		}

		return $result;
	}
}
