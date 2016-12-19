<?php
/**
 * Edit Gig administration screen integration.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Edit Gig administration screen.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_Screen_EditGig extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'load-post.php',                array( $this, 'load_screen' ) );
		add_action( 'load-post-new.php',            array( $this, 'load_screen' ) );
		add_action( 'add_meta_boxes_bandstand_gig', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_bandstand_gig',      array( $this, 'on_gig_save' ), 10, 2 );
	}

	/**
	 * Set up the screen.
	 *
	 * @since 1.0.0
	 */
	public function load_screen() {
		if ( 'bandstand_gig' !== get_current_screen()->id ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'edit_form_after_title', array( $this, 'display_edit_fields' ) );
		add_action( 'admin_footer',          array( $this, 'print_templates' ) );
	}

	/**
	 * Register gig meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The gig post object being edited.
	 */
	public function register_meta_boxes( $post ) {
		add_meta_box(
			'bandstand-gig-tickets',
			esc_html__( 'Tickets', 'bandstand' ),
			array( $this, 'display_tickets_meta_box' ),
			'bandstand_gig',
			'side',
			'default'
		);
	}

	/**
	 * Enqueue assets for the Edit Gig screen.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'bandstand-gig-edit' );
		wp_enqueue_style( 'bandstand-venue-manager' );
	}

	/**
	 * Set up and display the main gig fields for editing.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_edit_fields( $post ) {
		$gig = get_bandstand_gig( $post );

		$time_format = $this->compatible_time_format();
		$venue       = $gig->has_venue() ? $gig->get_venue() : null;
		$venue_id    = $venue ? $venue->ID : 0;

		$all_day_text = esc_html__( 'All Day', 'bandstand' );
		$gig_time     = $gig->is_all_day() ? $all_day_text : $gig->format_start_date( '', $time_format );

		$venue = null;
		if ( ! empty( $venue_id ) ) {
			$venue = bandstand_rest_request( 'GET', '/bandstand/v1/venues/' . $venue_id, array(
				'context' => 'edit',
			) );
		}

		wp_localize_script( 'bandstand-gig-edit', '_bandstandGigEditSettings', array(
			'l10n'       => array(
				'allDay' => $all_day_text,
			),
			'venue'      => $venue,
			'timeFormat' => $time_format,
		) );

		wp_nonce_field( 'update-gig_' . $post->ID, 'bandstand_gig_nonce' );
		require( $this->plugin->get_path( 'admin/views/edit-gig.php' ) );
	}

	/**
	 * Gig tickets meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The gig post object being edited.
	 */
	public function display_tickets_meta_box( $post ) {
		?>
		<p class="bandstand-field">
			<label for="gig-tickets-price"><?php esc_html_e( 'Price:', 'bandstand' ); ?></label><br>
			<input type="text" name="gig_tickets_price" id="gig-tickets-price" value="<?php echo esc_attr( $post->bandstand_tickets_price ); ?>" class="large-text">
		</p>
		<p class="bandstand-field">
			<label for="gig-tickets-url"><?php esc_html_e( 'Tickets URL:', 'bandstand' ); ?></label><br>
			<input type="text" name="gig_tickets_url" id="gig-tickets-url" value="<?php echo esc_attr( $post->bandstand_tickets_url ); ?>" class="large-text">
		</p>
		<?php
	}

	/**
	 * Print Underscore.js templates.
	 *
	 * @since 1.0.0
	 */
	public function print_templates() {
		include( $this->plugin->get_path( 'admin/views/templates-gig.php' ) );
		include( $this->plugin->get_path( 'admin/views/templates-venue.php' ) );
	}

	/**
	 * Process and save gig info when the CPT is saved.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Gig post ID.
	 * @param WP_Post $post Gig post object.
	 */
	public function on_gig_save( $post_id, $post ) {
		static $is_active = false; // Prevent recursion.

		$is_autosave    = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = isset( $_POST['bandstand_gig_nonce'] ) && wp_verify_nonce( $_POST['bandstand_gig_nonce'], 'update-gig_' . $post_id );

		// Bail if the data shouldn't be saved or intention can't be verified.
		if ( $is_active || $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		if ( ! isset( $_POST['bandstand_gig'] ) ) {
			return;
		}

		$is_active = true;

		$data = array_merge( $_POST['bandstand_gig'], array( 'ID' => $post_id ) );

		$datetime = new DateTime( $data['start_date'] . ' ' . $data['start_time'] );
		$data['start_date'] = $datetime->format( 'Y-m-d' );
		$data['start_time'] = empty( $data['start_time'] ) ? '' : $datetime->format( 'H:i:s' );

		// Handle all-day events.
		if ( ! empty( $data['is_all_day'] ) ) {
			// Blank out the start time and end times.
			$data['start_time'] = $data['end_time'] = '';
		}

		$gig = save_bandstand_gig( $data );

		update_post_meta( $post_id, 'bandstand_sort_datetime_utc', $gig->generate_sort_time(), true );
		update_post_meta( $post_id, 'bandstand_upcoming_until_utc', $gig->generate_upcoming_until_time(), true );

		//update_post_meta( $post_id, 'bandstand_tickets_price', sanitize_text_field( $_POST['gig_tickets_price'] ) );
		//update_post_meta( $post_id, 'bandstand_tickets_url', esc_url_raw( $_POST['gig_tickets_url'] ) );

		$is_active = false;
	}

	/**
	 * Attempt to make custom time formats more compatible between JavaScript and PHP.
	 *
	 * If the time format option has an escape sequences, use a default format
	 * determined by whether or not the option uses 24 hour format or not.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function compatible_time_format() {
		$time_format = get_option( 'time_format' );

		if ( false !== strpos( $time_format, '\\' ) ) {
			$time_format = ( false !== strpbrk( $time_format, 'GH' ) ) ? 'G:i' : 'g:i a';
		}

		return $time_format;
	}
}
