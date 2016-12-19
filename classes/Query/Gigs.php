<?php
/**
 * Gig query.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Extend WP_Query and set some default arguments when querying for gigs.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 * @link    https://bradt.ca/blog/extending-wp_query/
 */
class Bandstand_Query_Gigs extends WP_Query {
	/**
	 * Build the query args.
	 *
	 * @since 1.0.0
	 *
	 * @todo Add context arg.
	 * @see bandstand_gig_query()
	 *
	 * @param array $args WP_Query args.
	 */
	public function query( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'post_status'         => 'publish',
			'posts_per_page'      => get_option( 'posts_per_page' ),
			'meta_key'            => 'bandstand_sort_datetime_utc',
			'orderby'             => 'meta_value',
			'order'               => 'asc',
			'ignore_sticky_posts' => true,
			'meta_query'          => array(
				array(
					'key'     => 'bandstand_upcoming_until_utc',
					'value'   => current_time( 'mysql', true ),
					'compare' => '>=',
					'type'    => 'DATETIME',
				),
			),
		) );

		$args = apply_filters( 'bandstand_gig_query_args', $args );
		$args['post_type'] = 'bandstand_gig';

		parent::query( $args );
	}
}
