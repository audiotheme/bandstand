<?php
/**
 * Edit Venue administration screen integration.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Edit Venue administration screen.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_Screen_EditVenue extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'load-post.php',                  array( $this, 'load_screen' ) );
		add_action( 'load-post-new.php',              array( $this, 'load_screen' ) );
		add_action( 'add_meta_boxes_bandstand_venue', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_bandstand_venue',      array( $this, 'on_venue_save' ), 10, 2 );
	}

	/**
	 * Set up the screen.
	 *
	 * @since 1.0.0
	 */
	public function load_screen() {
		if ( 'bandstand_venue' !== get_current_screen()->id ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'edit_form_after_title', array( $this, 'display_edit_fields' ) );
	}

	/**
	 * Register venue meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The venue post object being edited.
	 */
	public function register_meta_boxes( $post ) {
		add_meta_box(
			'bandstand-venue-notes',
			esc_html_x( 'Additional Notes', 'venue meta box title', 'bandstand' ),
			array( $this, 'display_notes_meta_box' ),
			'bandstand_venue',
			'normal',
			'core'
		);
	}

	/**
	 * Enqueue assets for the Edit Venue screen.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'bandstand-venue-edit' );
	}

	/**
	 * Set up and display the main venue fields for editing.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_edit_fields( $post ) {
		$venue = get_bandstand_venue( $post );
		wp_nonce_field( 'update-venue_' . $venue->ID, 'bandstand_venue_nonce', true );
		require( $this->plugin->get_path( 'admin/views/edit-venue.php' ) );
	}

	/**
	 * Display venue notes meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Venue post object.
	 */
	public function display_notes_meta_box( $post ) {
		$venue = get_bandstand_venue( $post );
		$notes = format_to_edit( $venue->notes, user_can_richedit() );

		require( $this->plugin->get_path( 'admin/views/edit-venue-notes.php' ) );
	}

	/**
	 * Process and save venue info when the CPT is saved.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Venue post ID.
	 * @param WP_Post $post Venue post object.
	 */
	public function on_venue_save( $post_id, $post ) {
		static $is_active = false; // Prevent recursion.

		$is_autosave    = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = isset( $_POST['bandstand_venue_nonce'] ) && wp_verify_nonce( $_POST['bandstand_venue_nonce'], 'update-venue_' . $post_id );

		// Bail if the data shouldn't be saved or intention can't be verified.
		if ( $is_active || $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		$is_active = true;
		$data = array_merge( $_POST['bandstand_venue'], array( 'ID' => $post_id ) );
		save_bandstand_venue( $data )->update_gig_count();
		$is_active = false;
	}
}
