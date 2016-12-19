<?php
/**
 * Manage Records administration screen integration.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Manage Records administration screen.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_Screen_ManageRecords extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_filter( 'parse_query',                                   array( $this, 'admin_query' ) );
		add_filter( 'manage_edit-bandstand_record_columns',          array( $this, 'register_columns' ) );
		add_action( 'manage_edit-bandstand_record_sortable_columns', array( $this, 'register_sortable_columns' ) );
		add_action( 'manage_pages_custom_column',                    array( $this, 'display_columns' ), 10, 2 );
	}

	/**
	 * Custom sort records on the Manage Records screen.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query The main WP_Query object. Passed by reference.
	 */
	public function admin_query( $wp_query ) {
		if ( ! is_admin() || ! isset( $_GET['post_type'] ) || 'bandstand_record' !== $_GET['post_type'] ) {
			return;
		}

		$sortable_keys = array( 'release_date', 'tracks' );
		if ( empty( $_GET['orderby'] ) || ! in_array( $_GET['orderby'], $sortable_keys, true ) ) {
			return;
		}

		switch ( $_GET['orderby'] ) {
			case 'release_date' :
				$meta_key  = 'bandstand_release_date';
				break;
			case 'tracks' :
				$meta_key = 'bandstand_track_count';
				$orderby = 'meta_value_num';
				break;
		}

		$order  = isset( $_GET['order'] ) && 'desc' === $_GET['order'] ? 'desc' : 'asc';
		$orderby = empty( $orderby ) ? 'meta_value' : $orderby;

		$wp_query->set( 'meta_key', $meta_key );
		$wp_query->set( 'orderby', $orderby );
		$wp_query->set( 'order', $order );
	}

	/**
	 * Register record columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns An array of the column names to display.
	 * @return array Filtered array of column names.
	 */
	public function register_columns( $columns ) {
		$columns['title'] = esc_html_x( 'Record', 'column_name', 'bandstand' );

		// Create columns and insert them in the appropriate position in the columns array.
		$image_column = array( 'bandstand_image' => esc_html_x( 'Image', 'column name', 'bandstand' ) );
		$columns = bandstand_array_insert_after_key( $columns, 'cb', $image_column );

		$release_column = array( 'release_date' => esc_html_x( 'Released', 'column_name', 'bandstand' ) );
		$columns = bandstand_array_insert_after_key( $columns, 'title', $release_column );

		$columns['track_count'] = esc_html_x( 'Tracks', 'column name', 'bandstand' );

		unset( $columns['date'] );

		return $columns;
	}

	/**
	 * Register sortable record columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Column query vars with their corresponding column id as the key.
	 * @return array
	 */
	public function register_sortable_columns( $columns ) {
		$columns['release_date'] = 'release_date';
		$columns['track_count']  = 'tracks';
		return $columns;
	}

	/**
	 * Display custom record columns.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name The id of the column to display.
	 * @param int    $post_id Post ID.
	 */
	public function display_columns( $column_name, $post_id ) {
		switch ( $column_name ) {
			case 'release_date' :
				echo esc_html( get_bandstand_record( $post_id )->get_release_date( get_option( 'date_format' ) ) );
				break;

			case 'track_count' :
				$args = array(
					'post_type'   => 'bandstand_track',
					'post_parent' => $post_id,
				);

				printf(
					'<a href="%1$s">%2$s</a>',
					esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) ),
					absint( get_bandstand_record( $post_id )->get_track_count() )
				);
				break;
		}
	}
}
