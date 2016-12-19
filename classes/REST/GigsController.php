<?php
/**
 * Gigs REST controller.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Gigs REST controller class.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_REST_GigsController extends Bandstand_REST_AbstractPostsController {
	/**
	 * Retrieve gigs.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$args                   = array();
		$args['post_type']      = $this->post_type;
		$args['date_query']     = array();
		$args['meta_query']     = array();
		$args['offset']         = $request['offset'];
		$args['order']          = $request['order'];
		$args['orderby']        = $request['orderby'];
		$args['paged']          = $request['page'];
		$args['post__in']       = $request['include'];
		$args['post__not_in']   = $request['exclude'];
		$args['posts_per_page'] = $request['per_page'];
		$args['post_status']    = $request['status'];
		$args['s']              = $request['search'];

		if ( ! empty( $request['after'] ) ) {
			$args['meta_query'][] = array(
				'key'     => 'bandstand_start_date',
				'value'   => $request['after'],
				'compare' => '>',
				'type'    => 'DATE',
			);
		}

		if ( ! empty( $request['before'] ) ) {
			$args['meta_query'][] = array(
				'key'     => 'bandstand_start_date',
				'value'   => $request['before'],
				'compare' => '<',
				'type'    => 'DATE',
			);
		}

		if ( ! empty( $request['date'] ) ) {
			$args['meta_query'][] = array(
				'key'     => 'bandstand_start_date',
				'value'   => $request['date'],
				'compare' => '=',
				'type'    => 'DATE',
			);
		}

		if ( 'created' === $request['orderby'] ) {
			$args['orderby'] = 'date';
		} elseif ( 'date' === $request['orderby'] ) {
			$args['meta_key'] = 'bandstand_sort_datetime_utc';
			$args['orderby'] = 'meta_value';
		} elseif ( 'include' === $request['orderby'] ) {
			$args['orderby'] = 'post__in';
		}

		if ( ! empty( $request['venue'] ) ) {
			$args['meta_query'][] = array(
				'key'   => 'bandstand_venue_id',
				'value' => $request['venue'],
			);
		}

		$query = new WP_Query( $args );

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

		$datetime = new DateTime( $data['start_date'] . ' ' . $data['start_time'] );
		$data['start_date'] = $datetime->format( 'Y-m-d' );
		$data['start_time'] = empty( $data['start_time'] ) ? '' : $datetime->format( 'H:i:s' );

		$gig = save_bandstand_gig( $data );

		update_post_meta( $post->ID, 'bandstand_sort_datetime_utc', $gig->generate_sort_time(), true );
		update_post_meta( $post->ID, 'bandstand_upcoming_until_utc', $gig->generate_upcoming_until_time(), true );
	}

	/**
	 * Prepare a single gig for create or update.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_Error|stdClass Post object.
	 */
	protected function prepare_item_for_database( $request ) {
		$post = new stdClass;

		if ( isset( $request['id'] ) ) {
			$post->ID = absint( $request['id'] );
		}

		if ( isset( $request['status'] ) ) {
			$status = $this->handle_status_param( $request['status'], get_post_type_object( $this->post_type ) );
			if ( is_wp_error( $status ) ) {
				return $status;
			}

			$post->post_status = $status;
		}

		if ( isset( $request['title'] ) ) {
			$post->post_title = wp_filter_post_kses( $request['title'] );
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
		$gig = get_bandstand_gig( $post );

		$data = array(
			'id'             => $gig->ID,
			'description'    => $gig->get_post()->post_excerpt,
			'start_date'     => $gig->format_start_date( 'Y-m-d', '', array( 'translate' => false ) ),
			'start_time'     => $gig->format_start_date( '', 'H:i:s', array( 'translate' => false ) ),
			'start_datetime' => $gig->format_start_date( 'c', '', array( 'translate' => false ) ),
			'timezone_id'    => $gig->get_timezone_id(),
			'title'          => $gig->get_title(),
			'venue_id'       => $gig->has_venue() ? $gig->get_venue()->ID : '',
			//'created'        => date( 'c', strtotime( $gig->post_date_gmt ) ),
			//'modified'       => date( 'c', strtotime( $gig->post_modified_gmt ) ),
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

		$gig = get_bandstand_gig( $post );

		if ( $gig->has_venue() ) {
			$links['venue'] = array(
				'href'       => rest_url( sprintf( '/bandstand/v1/venues/%d', $gig->get_venue()->ID ) ),
				'embeddable' => true,
			);
		}

		return $links;
	}

	/**
	 * Get the gig schema, conforming to JSON Schema.
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
				'description'    => array(
					'description' => esc_html__( 'A short description of the gig.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => 'wp_filter_post_kses',
					),
				),
				'id'             => array(
					'description' => esc_html__( 'Unique identifier for the gig.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'start_date'     => array(
					'description' => esc_html__( 'The date the gig starts.', 'bandstand' ),
					'required'    => true,
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'start_time'     => array(
					'description' => esc_html__( 'The time the gig starts.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'start_datetime' => array(
					'description' => esc_html__( 'The date and time the gig starts.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'status'         => array(
					'description' => esc_html__( 'A named status for the gig.', 'bandstand' ),
					'type'        => 'string',
					'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
					'context'     => array( 'edit' ),
				),
				'timezone_id'    => array(
					'description' => esc_html__( 'The time zone the gig occurs in.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'title'          => array(
					'description' => esc_html__( 'The title of the gig.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'venue_id'       => array(
					'description' => esc_html__( 'The id for the venue where the gig occurs.', 'bandstand' ),
					'required'    => true,
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
			),
		);
	}

	/**
	 * Get the query params for collections of venues.
	 *
	 * @since 1.0.0
	 *
	 * @todo Add created_before, created_after, modified_before, modified_after parameters.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['orderby']['default'] = 'date';
		$params['orderby']['enum'][] = 'date';

		$params['after'] = array(
			'description'       => esc_html__( 'Limit results to gigs occurring after a date.', 'bandstand' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_date' ),
		);

		$params['before'] = array(
			'description'       => esc_html__( 'Limit results to gigs occurring before a date.', 'bandstand' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_date' ),
		);

		$params['date'] = array(
			'description'       => esc_html__( 'Limit results to gigs occurring on a date.', 'bandstand' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_date' ),
		);

		$params['venue'] = array(
			'description'       => esc_html__( 'Limit results to gigs that occur at a venue.', 'bandstand' ),
			'required'          => false,
			'type'              => 'integer',
		);

		ksort( $params );

		return $params;
	}
}
