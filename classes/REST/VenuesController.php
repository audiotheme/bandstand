<?php
/**
 * Venues REST controller.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Venues REST controller class.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_REST_VenuesController extends Bandstand_REST_AbstractPostsController {
	/**
	 * Retrieve venues.
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
		$args['s']              = $request['search'];

		if ( 'created' === $request['orderby'] ) {
			$args['orderby'] = 'date';
		} elseif ( 'include' === $request['orderby'] ) {
			$args['orderby'] = 'post__in';
		} elseif ( 'name' === $request['orderby'] ) {
			$args['orderby'] = 'title';
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
		save_bandstand_venue( $data );
	}

	/**
	 * Prepare a single venue for create or update.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_Error|stdClass Post object.
	 */
	protected function prepare_item_for_database( $request ) {
		$post = parent::prepare_item_for_database( $request );

		if ( isset( $request['name'] ) ) {
			$post->post_title = wp_filter_post_kses( $request['name'] );
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
		$venue = get_bandstand_venue( $post );

		$data = array(
			'id'          => $venue->ID,
			'address'     => $venue->get_address(),
			'city'        => $venue->get_city(),
			'country'     => $venue->get_country(),
			'gig_count'   => $venue->get_gig_count(),
			'latitude'    => $venue->get_latitude(),
			'longitude'   => $venue->get_longitude(),
			'name'        => $venue->get_name(),
			'phone'       => $venue->get_phone(),
			'postal_code' => $venue->get_postal_code(),
			'region'      => $venue->get_region(),
			'timezone_id' => $venue->get_timezone_id(),
			'website_url' => $venue->get_website_url(),
		);

		if ( 'edit' === $request['context'] ) {
			$data['status']      = $post->post_status;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $post ) );

		return $response;
	}

	/**
	 * Get the venue schema, conforming to JSON Schema.
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
				'address'       => array(
					'description' => esc_html__( 'The venue street address.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'city'         => array(
					'description' => esc_html__( 'The venue city.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'country'       => array(
					'description' => esc_html__( 'The venue country.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'gig_count'     => array(
					'description' => esc_html__( 'The number of gigs assigned to the venue.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'id'            => array(
					'description' => esc_html__( 'Unique identifier for the venue.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'latitude'      => array(
					'description' => esc_html__( 'The venue latitude.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'longitude'     => array(
					'description' => esc_html__( 'The venue longitude.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'name'          => array(
					'description' => esc_html__( 'The name of the venue.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'postal_code'   => array(
					'description' => esc_html__( 'The venue postal code.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'region'        => array(
					'description' => esc_html__( 'The venue region.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'status'        => array(
					'description' => esc_html__( 'A named status for the venue.', 'bandstand' ),
					'type'        => 'string',
					'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
					'context'     => array( 'edit' ),
				),
				'timezone_id'   => array(
					'description' => esc_html__( 'The venue time zone.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'website_url'   => array(
					'description' => esc_html__( 'The venue website URL.', 'bandstand' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);
	}

	/**
	 * Get the query params for collections of venues.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['order']['default'] = 'asc';
		$params['orderby']['default'] = 'name';
		$params['orderby']['enum'][] = 'name';

		return $params;
	}
}
