<?php
/**
 * Base posts REST controller.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Base posts REST controller class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
abstract class Bandstand_REST_AbstractPostsController extends WP_REST_Posts_Controller {
	/**
	 * Constructor method.
	 *
	 * @param string $namespace Route namespace.
	 * @param string $base      Route base.
	 * @param string $post_type Post type name.
	 */
	public function __construct( $namespace, $base, $post_type ) {
		$this->namespace = $namespace;
		$this->rest_base = $base;
		$this->post_type = $post_type;
	}

	/**
	 * Retrieve posts.
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
	 * Add pagination headers to collection responses.
	 *
	 * @since 1.0.0
	 *
	 * @param int              $total    Total items in the response collection.
	 * @param array            $args     Query arguments.
	 * @param WP_REST_Response $response Response object.
	 * @param WP_REST_Request  $request  Request object.
	 */
	protected function handle_collection_pagination( $total, $args, $response, $request ) {
		$page = (int) $args['paged'];

		if ( $total < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $args['paged'] );

			$count_query = new WP_Query();
			$count_query->query( $args );
			$total = $count_query->found_posts;
		}

		$max_pages = ceil( $total / (int) $args['posts_per_page'] );

		$response->header( 'X-WP-Total', (int) $total );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		$base = add_query_arg( $request_params, rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}

		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}
	}

	/**
	 * Get the query params for collections of venues.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = wp_array_slice_assoc( parent::get_collection_params(), array(
			'context', 'exclude', 'include', 'offset', 'order', 'page',
			'per_page', 'search', 'status',
		) );

		$params['orderby'] = array(
			'description'        => esc_html__( 'Sort collection by object attribute.', 'bandstand' ),
			'type'               => 'string',
			'default'            => 'created',
			'enum'               => array(
				'created',
				'id',
				'include',
				'modified',
				'title',
			),
			'validate_callback'  => 'rest_validate_request_arg',
		);

		ksort( $params );

		return $params;
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
		$base = sprintf( '/%s/%s', $this->namespace, $this->rest_base );

		$links = array(
			'self' => array(
				'href'   => rest_url( trailingslashit( $base ) . $post->ID ),
			),
			'collection' => array(
				'href'   => rest_url( $base ),
			),
		);

		return $links;
	}

	/**
	 * Check if a given post type should be viewed or managed.
	 *
	 * @since 1.0.0
	 *
	 * @param  object|string $post_type Post type.
	 * @return boolean Is post type allowed?
	 */
	protected function check_is_post_type_allowed( $post_type ) {
		return true;
	}

	/**
	 * Validate a date string.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value Date string.
	 * @return boolean
	 */
	public function validate_date( $value ) {
		$d = date_parse( $value );
		return $d['error_count'] < 1;
	}
}
