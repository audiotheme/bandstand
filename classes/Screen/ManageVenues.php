<?php
/**
 * Manage Venues administration screen integration.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Manage Venues administration screen.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_Screen_ManageVenues extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_filter( 'parse_query',                                  array( $this, 'parse_admin_query' ) );
		add_filter( 'manage_bandstand_venue_posts_columns',         array( $this, 'register_columns' ) );
		add_action( 'manage_posts_custom_column',                   array( $this, 'display_columns' ), 10, 2 );
		add_action( 'manage_edit-bandstand_venue_sortable_columns', array( $this, 'register_sortable_columns' ) );
	}

	/**
	 * Sort posts on the Manage Venues screen.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query The main WP_Query object passed by reference.
	 */
	public function parse_admin_query( $wp_query ) {
		// Ensure this only affects requests in the admin panel.
		if (
			! is_admin() ||
			empty( $_GET['post_type'] ) ||
			'bandstand_venue' !== $_GET['post_type']
		) {
			return;
		}

		$orderby = empty( $_REQUEST['orderby'] ) ? 'title' : sanitize_key( $_REQUEST['orderby'] );
		$order   = isset( $_REQUEST['order'] ) && 'desc' === strtolower( $_REQUEST['order'] ) ? 'desc' : 'asc';
		$wp_query->set( 'order', $order );

		switch ( $orderby ) {
			case 'title':
				$wp_query->set( 'orderby', 'title' );
				break;
			case 'gigs':
				$wp_query->set( 'meta_key', 'bandstand_gig_count' );
				$wp_query->set( 'orderby', 'meta_value_num' );
				break;
			case 'city':
			case 'country':
			case 'phone':
			case 'region':
				$wp_query->set( 'meta_key', 'bandstand_' . $orderby );
				$wp_query->set( 'orderby', 'meta_value' );
				break;
		}
	}

	/**
	 * Remove 'Edit' from the bulk edit options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions List of actions.
	 * @return array
	 */
	public function bulk_actions( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	/**
	 * Register list table columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns An array of the column names to display.
	 * @return array Filtered array of column names.
	 */
	public function register_columns( $columns ) {
		$columns['title']     = esc_html_x( 'Name', 'column name', 'bandstand' );
		$columns['city']      = esc_html__( 'City', 'bandstand' );
		$columns['region']    = esc_html__( 'State', 'bandstand' );
		$columns['country']   = esc_html__( 'Country', 'bandstand' );
		$columns['phone']     = esc_html__( 'Phone', 'bandstand' );
		$columns['gig_count'] = esc_html__( 'Gigs', 'bandstand' );

		$columns['website']   = sprintf(
			'<span class="bandstand-column-header-icon dashicons dashicons-admin-links"></span><span class="bandstand-column-header-label">%s</span>',
			esc_html__( 'Website', 'bandstand' )
		);

		unset( $columns['date'] );

		return $columns;
	}

	/**
	 * Register sortable list table columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Column query vars with their corresponding column id as the key.
	 * @return array
	 */
	public function register_sortable_columns( $columns ) {
		$columns['title']     = array( 'title', true );
		$columns['city']      = 'city';
		$columns['region']    = 'region';
		$columns['country']   = 'country';
		$columns['phone']     = 'phone';
		$columns['gig_count'] = 'gig_count';

		return $columns;
	}

	/**
	 * Display custom list table columns.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_id The id of the column to display.
	 * @param int    $post_id Post ID.
	 */
	public function display_columns( $column_id, $post_id ) {
		if ( 'edit-bandstand_venue' !== get_current_screen()->id ) {
			return;
		}

		$post = get_post( $post_id );

		switch ( $column_id ) {
			case 'gig_count' :
				echo $this->get_gig_count_column_value( $post );
				break;
			case 'website' :
				echo $this->get_website_column_value( $post );
				break;
			default :
				$venue = get_bandstand_venue( $post );

				if ( method_exists( $venue, 'get_' . $column_id ) ) {
					echo esc_html( call_user_func( array( $venue, 'get_' . $column_id ) ) );
				}
		}
	}

	/**
	 * Filter post row actions.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $actions Post actions.
	 * @param WP_Post $post    Post object.
	 * @return array
	 */
	public function post_row_actions( $actions, $post ) {
		if ( 'bandstand_venue' === get_post_type( $post ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	/**
	 * Retrieve the value for the gig count column.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Venue post object.
	 * @return string
	 */
	protected function get_gig_count_column_value( $post ) {
		$count = get_bandstand_venue( $post )->get_gig_count();

		// @todo Update post status?
		$admin_url = add_query_arg( array(
			'post_type'   => 'bandstand_gig',
			'post_status' => 'any',
			'venue'       => $post->ID,
		), admin_url( 'edit.php' ) );

		$admin_link = sprintf(
			'<a href="%s">%d</a>',
			esc_url( $admin_url ),
			$count
		);

		return empty( $count ) ? $count : $admin_link;
	}

	/**
	 * Retrieve the value for the URL column.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Venue post object.
	 * @return string
	 */
	protected function get_website_column_value( $post ) {
		$url = '';
		$venue = get_bandstand_venue( $post );

		if ( $venue->has_website_url() ) {
			$url = sprintf(
				'<a href="%s" class="venue-website-link" target="_blank"><span class="dashicons dashicons-admin-links"></span><span class="screen-reader-text">%s</span></a>',
				esc_url( $venue->get_website_url() ),
				esc_attr__( 'Visit venue website', 'bandstand' )
			);
		}

		return $url;
	}
}
