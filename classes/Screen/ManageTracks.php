<?php
/**
 * Manage Tracks administration screen integration.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Manage Tracks administration screen.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_Screen_ManageTracks extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_filter( 'parse_query',                                  array( $this, 'admin_query' ) );
		add_action( 'restrict_manage_posts',                        array( $this, 'list_table_filters' ) );
		add_filter( 'manage_edit-bandstand_track_columns',          array( $this, 'register_columns' ) );
		add_action( 'manage_edit-bandstand_track_sortable_columns', array( $this, 'register_sortable_columns' ) );
		add_action( 'manage_posts_custom_column',                   array( $this, 'display_columns' ), 10, 2 );
	}

	/**
	 * Custom sort tracks on the Manage Tracks screen.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query The main WP_Query object. Passed by reference.
	 */
	public function admin_query( $wp_query ) {
		if ( ! isset( $_GET['post_type'] ) || 'bandstand_track' !== $_GET['post_type'] ) {
			return;
		}

		$sortable_keys = array( 'artist' );
		if ( ! empty( $_GET['orderby'] ) && in_array( $_GET['orderby'], $sortable_keys, true ) ) {
			switch ( $_GET['orderby'] ) {
				case 'artist' :
					$meta_key = 'bandstand_artist';
					break;
			}

			$order = isset( $_GET['order'] ) && 'desc' === $_GET['order'] ? 'desc' : 'asc';
			$orderby = empty( $orderby ) ? 'meta_value' : $orderby;

			$wp_query->set( 'meta_key', $meta_key );
			$wp_query->set( 'orderby', $orderby );
			$wp_query->set( 'order', $order );
		} elseif ( empty( $_GET['orderby'] ) ) {
			// Auto-sort tracks by title.
			$wp_query->set( 'orderby', 'title' );
			$wp_query->set( 'order', 'asc' );
		}

		if ( ! empty( $_GET['post_parent'] ) ) {
			$wp_query->set( 'post_parent', absint( $_GET['post_parent'] ) );
		}
	}

	/**
	 * Custom track filter dropdowns.
	 *
	 * @since 1.0.0
	 */
	public function list_table_filters() {
		global $wpdb;

		$screen = get_current_screen();
		$post_parent = empty( $_GET['post_parent'] ) ? 0 : absint( $_GET['post_parent'] );

		if ( 'edit-bandstand_track' !== $screen->id ) {
			return;
		}

		$records = $wpdb->get_results(
			"SELECT ID, post_title
			FROM $wpdb->posts
			WHERE post_type = 'bandstand_record' AND post_status != 'auto-draft'
			ORDER BY post_title ASC"
		);
		?>
		<select name="post_parent">
			<option value="0"><?php esc_html_e( 'View all records', 'bandstand' ); ?></option>
			<?php
			if ( $records ) {
				foreach ( $records as $record ) {
					printf(
						'<option value="%1$d"%2$s>%3$s</option>',
						esc_attr( $record->ID ),
						selected( $post_parent, $record->ID, false ),
						esc_html( $record->post_title )
					);
				}
			}
			?>
		</select>
		<?php
	}

	/**
	 * Register track columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns An array of the column names to display.
	 * @return array The filtered array of column names.
	 */
	public function register_columns( $columns ) {
		$columns['title'] = esc_html_x( 'Track', 'column_name', 'bandstand' );

		$track_columns = array(
			'artist'   => esc_html_x( 'Artist', 'column name', 'bandstand' ),
			'record'   => esc_html_x( 'Record', 'column name', 'bandstand' ),
			'stream'   => esc_html_x( 'Stream', 'column name', 'bandstand' ),
			'download' => esc_html_x( 'Download', 'column name', 'bandstand' ),
			'purchase' => esc_html_x( 'Purchase URL', 'column name', 'bandstand' ),
		);

		$columns = bandstand_array_insert_after_key( $columns, 'title', $track_columns );

		unset( $columns['date'] );

		return $columns;
	}

	/**
	 * Register sortable track columns.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Column query vars with their corresponding column id as the key.
	 * @return array
	 */
	public function register_sortable_columns( $columns ) {
		$columns['artist']      = 'artist';
		$columns['track_count'] = 'tracks';
		//$columns['download']    = 'download';
		//$columns['stream']      = 'stream';

		return $columns;
	}

	/**
	 * Display custom track columns.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name The id of the column to display.
	 * @param int    $post_id Post ID.
	 */
	public function display_columns( $column_name, $post_id ) {
		switch ( $column_name ) {
			case 'artist' :
				echo esc_html( get_bandstand_track( $post_id )->get_artist() );
				break;

			case 'download' :
				if ( get_bandstand_track( $post_id )->is_downloadable() ) {
					printf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_url( get_bandstand_track( $post_id )->get_download_url() ),
						'<span class="dashicons dashicons-download"></span>'
					);
				}
				break;

			case 'purchase' :
				$track = get_bandstand_track( $post_id );
				if ( $track->has_purchase_url() ) {
					printf(
						'<a href="%1$s" target="_blank" class="dashicons dashicons-admin-links"><span class="screen-reader-text">%2$s</span></a>',
						esc_url( $track->get_purchase_url() ),
						esc_html__( 'Purchase Link', 'bandstand' )
					);
				}
				break;

			case 'record' :
				$track  = get_post( $post_id );
				$record = get_post( $track->post_parent );

				if ( $track->post_parent && $record ) {
					printf(
						'<a href="%1$s">%2$s</a>',
						esc_url( get_edit_post_link( $record->ID ) ),
						esc_html( wp_strip_all_tags( apply_filters( 'the_title', $record->post_title ) ) )
					);
				}
				break;

			case 'stream' :
				if ( get_bandstand_track( $post_id )->is_streamable() ) {
					printf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_url( get_bandstand_track( $post_id )->get_stream_url() ),
						'<span class="dashicons dashicons-format-audio"></span>'
					);
				}
				break;
		}
	}
}
