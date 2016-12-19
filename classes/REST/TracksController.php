<?php
/**
 * Tracks REST controller.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Tracks REST controller class.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_REST_TracksController extends Bandstand_REST_AbstractPostsController {
	/**
	 * Retrieve tracks.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$args                   = array();
		$args['post_type']      = $this->post_type;
		$args['offset']         = $request['offset'];
		$args['order']          = $request['order'];
		$args['orderby']        = $request['orderby'];
		$args['paged']          = $request['page'];
		$args['post__in']       = $request['include'];
		$args['post__not_in']   = $request['exclude'];
		$args['posts_per_page'] = $request['per_page'];
		$args['post_status']    = $request['status'];

		if ( 'created' === $request['orderby'] ) {
			$args['orderby'] = 'date';
		} elseif ( 'include' === $request['orderby'] ) {
			$args['orderby'] = 'post__in';
		}

		if ( ! empty( $request['record'] ) ) {
			$args['post_parent'] = $request['record'];
			$args['posts_per_page'] = -1;
			$args['orderby'] = 'menu_order';
			$args['order'] = 'asc';
		}

		$query = new WP_Query( $args );

		$items = array();
		foreach ( $query->posts as $post ) {
			if ( ! $this->check_read_permission( $post ) ) {
				continue;
			}

			$data = $this->prepare_item_for_response( $post, $request );
			$items[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $items );
		$this->handle_collection_pagination( $query->found_posts, $args, $response, $request );

		return $response;
	}

	/**
	 * Update the values of additional fields added to a data object.
	 *
	 * @param WP_Post         $post    Post object.
	 * @param WP_REST_Request $request Request instance.
	 */
	protected function update_additional_fields_for_object( $post, $request ) {
		$data = array_merge( $request->get_params(), array( 'ID' => $post->ID ) );
		save_bandstand_track( $data );
	}

	/**
	 * Prepare a single track for create or update.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_Error|stdClass Post object.
	 */
	protected function prepare_item_for_database( $request ) {
		$post = parent::prepare_item_for_database( $request );

		if ( isset( $request['record_id'] ) ) {
			$post->post_parent = absint( $request['record_id'] );
		}

		return $post;
	}

	/**
	 * Prepare a single post output for response.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_Post         $post    Post object.
	 * @param  WP_REST_Request $request HTTP request instance.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $post, $request ) {
		$track = get_bandstand_track( $post );

		$data = array(
			'id'           => $track->ID,
			'artist'       => $track->get_artist(),
			'download_url' => $track->get_download_url(),
			'duration'     => $track->get_duration(),
			'purchase_url' => $track->get_purchase_url(),
			'record_id'    => $post->post_parent,
			'stream_url'   => $track->get_stream_url(),
			'title'        => $track->get_title(),
			'track_number' => $track->get_track_number(),
		);

		if ( 'edit' === $request['context'] ) {
			$data['menu_order']  = $post->menu_order;
			$data['post_parent'] = $post->post_parent;
			$data['status']      = $post->post_status;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $post ) );

		return $response;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_Post $post Post object.
	 * @return array   Links for the given post.
	 */
	protected function prepare_links( $post ) {
		$links = parent::prepare_links( $post );

		if ( ! empty( $post->post_parent ) ) {
			$links['record'] = array(
				'href'       => rest_url( sprintf( '/bandstand/v1/records/%d', $post->post_parent ) ),
				'embeddable' => true,
			);
		}

		return $links;
	}

	/**
	 * Get the track schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_item_schema() {
		return array(
			'$schema'    => 'http://json-schema.org/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'artist'       => array(
					'description' => esc_html__( 'The track artist(s).', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'download_url' => array(
					'description' => esc_html__( 'The URL to download the track audio.', 'bandstand' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
				),
				'duration'     => array(
					'description' => esc_html__( 'The formatted track duration.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),

				'id'           => array(
					'description' => esc_html__( 'Unique identifier for the track.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'menu_order'   => array(
					'description' => esc_html__( 'The order the track appears within its parent record.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'purchase_url' => array(
					'description' => esc_html__( 'The URL where the track can be purchased.', 'bandstand' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
				),
				'record_id'    => array(
					'description' => esc_html__( 'The parent record id.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'stream_url'   => array(
					'description' => esc_html__( 'The URL for streaming the track.', 'bandstand' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
				),
				'status'       => array(
					'description' => esc_html__( 'A named status for the track.', 'bandstand' ),
					'type'        => 'string',
					'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
					'context'     => array( 'edit' ),
				),
				'title'        => array(
					'description' => esc_html__( 'The title of the track.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'track_number' => array(
					'description' => esc_html__( 'The track number.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			),
		);
	}

	/**
	 * Get the query params for collections of tracks.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['record'] = array(
			'description'       => esc_html__( 'The parent record id.', 'bandstand' ),
			'type'              => 'integer',
		);

		ksort( $params );

		return $params;
	}
}
