<?php
/**
 * Records REST controller.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Records REST controller class.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_REST_RecordsController extends Bandstand_REST_AbstractPostsController {
	/**
	 * Register routes.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route( $this->namespace, $this->rest_base . '/(?P<id>[\d]+)/tracks', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item_tracks' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'view', 'edit', 'embed' ) ),
				),
			),
		) );
	}

	/**
	 * Update the values of additional fields added to a data object.
	 *
	 * @param WP_Post         $post    Post object.
	 * @param WP_REST_Request $request Request instance.
	 */
	protected function update_additional_fields_for_object( $post, $request ) {
		$data = array_merge( $request->get_params(), array( 'ID' => $post->ID ) );
		save_bandstand_record( $data );
	}

	/**
	 * Get a single record's tracks.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item_tracks( $request ) {
		$id = (int) $request['id'];
		$post = get_post( $id );

		if ( empty( $id ) || empty( $post->ID ) || $this->post_type !== $post->post_type ) {
			return new WP_Error( 'rest_post_invalid_id', esc_html__( 'Invalid post id.', 'bandstand' ), array( 'status' => 404 ) );
		}

		$response = bandstand_rest_request( 'GET', '/bandstand/v1/tracks', array(
			'record' => $id,
		) );

		return $response;
	}

	/**
	 * Prepare a single post output for response.
	 *
	 * @param  WP_Post         $post    Post object.
	 * @param  WP_REST_Request $request HTTP request instance.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $post, $request ) {
		$record = get_bandstand_record( $post );

		$data = array(
			'id'             => $record->ID,
			'artist'         => $record->get_artist(),
			'catalog_number' => $record->get_catalog_number(),
			'label'          => $record->get_label(),
			'release_date'   => $record->get_release_date( 'Y-m-d' ),
			'title'          => $record->get_title(),
			'track_count'    => $record->get_track_count(),
		);

		if ( 'edit' === $request['context'] ) {
			$data['status'] = $post->post_status;
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

		$links['tracks'] = array(
			'href'       => rest_url( sprintf( '/bandstand/v1/records/%d/tracks', $post->ID ) ),
			'embeddable' => true,
		);

		return $links;
	}

	/**
	 * Get the record schema, conforming to JSON Schema.
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
				'artist'         => array(
					'description' => esc_html__( 'The primary record artist(s).', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'catalog_number' => array(
					'description' => esc_html__( 'The catalog number.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'id'             => array(
					'description' => esc_html__( 'Unique identifier for the record.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'label'          => array(
					'description' => esc_html__( 'The record label.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'release_date'   => array(
					'description' => esc_html__( 'The release date or year.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'status'         => array(
					'description' => esc_html__( 'A named status for the record.', 'bandstand' ),
					'type'        => 'string',
					'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
					'context'     => array( 'edit' ),
				),
				'title'          => array(
					'description' => esc_html__( 'The title of the record.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'track_count'    => array(
					'description' => esc_html__( 'The nubmer of tracks associated with the record.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);
	}

	/**
	 * Get the query params for collections of records.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['orderby']['default'] = 'release_date';
		$params['orderby']['enum'][] = 'release_date';

		return $params;
	}
}
