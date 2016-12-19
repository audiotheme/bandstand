<?php
/**
 * Edit Track administration screen integration.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Edit Track administration screen.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_Screen_EditTrack extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'load-post.php',                  array( $this, 'load_screen' ) );
		add_action( 'load-post-new.php',              array( $this, 'load_screen' ) );
		add_action( 'add_meta_boxes_bandstand_track', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_bandstand_track',      array( $this, 'on_track_save' ) );
	}

	/**
	 * Set up the screen.
	 *
	 * @since 1.0.0
	 */
	public function load_screen() {
		if ( 'bandstand_track' !== get_current_screen()->id ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register record meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The record post object being edited.
	 */
	public function register_meta_boxes( $post ) {
		add_meta_box(
			'bandstand-track-details',
			esc_html__( 'Track Details', 'bandstand' ),
			array( $this, 'display_details_meta_box' ),
			'bandstand_track',
			'side',
			'default'
		);

		if ( empty( $post->post_parent ) || ! get_post( $post->post_parent ) ) {
			return;
		}

		add_meta_box(
			'bandstand-track-related-tracks',
			esc_html__( 'Related Tracks', 'bandstand' ),
			array( $this, 'display_related_tracks_meta_box' ),
			'bandstand_track',
			'side',
			'low'
		);

		add_action( 'edit_form_after_title', array( $this, 'display_record_panel' ) );
	}

	/**
	 * Enqueue assets for the Edit Record screen.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'bandstand-media' );
	}

	/**
	 * Display a record panel.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The track post object being edited.
	 */
	public function display_record_panel( $post ) {
		$record                  = get_bandstand_record( $post->post_parent );
		$record_post_type_object = get_post_type_object( 'bandstand_record' );

		include( $this->plugin->get_path( 'admin/views/panel-track-record-details.php' ) );
}

	/**
	 * Display track details meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The track post object being edited.
	 */
	public function display_details_meta_box( $post ) {
		wp_nonce_field( 'update-track_' . $post->ID, 'bandstand_track_nonce' );
		require( $this->plugin->get_path( 'admin/views/meta-box-track-details.php' ) );
	}

	/**
	 * Display related tracks meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The track post object being edited.
	 */
	public function display_related_tracks_meta_box( $post ) {
		$record = get_bandstand_record( $post->post_parent );
		include( $this->plugin->get_path( 'admin/views/meta-box-track-related-tracks.php' ) );
	}

	/**
	 * Process and save track info when the CPT is saved.
	 *
	 * @since 1.0.0
	 * @todo Get ID3 info for remote files.
	 *
	 * @param int $post_id Post ID.
	 */
	public function on_track_save( $post_id ) {
		static $is_active = false; // Prevent recursion.

		$is_autosave    = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = isset( $_POST['bandstand_track_nonce'] ) && wp_verify_nonce( $_POST['bandstand_track_nonce'], 'update-track_' . $post_id );

		// Bail if the data shouldn't be saved or intention can't be verified.
		if ( $is_active || $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		$is_active = true;

		$data = array_merge( $_POST['bandstand_track'], array( 'ID' => $post_id ) );
		save_bandstand_track( $data );

		$is_active = false;
	}
}
