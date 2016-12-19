<?php
/**
 * Manage Videos administration screen integration.
 *
 * @package   Bandstand\Videos
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Manage Videos administration screen.
 *
 * @package Bandstand\Videos
 * @since   1.0.0
 */
class Bandstand_Screen_ManageVideos extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_filter( 'manage_edit-bandstand_video_columns', array( $this, 'register_columns' ) );
	}

	/**
	 * Register list table columns.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $columns An array of the column names to display.
	 * @return array Filtered array of column names.
	 */
	public function register_columns( $columns ) {
		// Register an image column and insert it after the checkbox column.
		$image_column = array( 'bandstand_image' => esc_html_x( 'Image', 'column name', 'bandstand' ) );
		$columns = bandstand_array_insert_after_key( $columns, 'cb', $image_column );

		return $columns;
	}
}
