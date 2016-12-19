<?php
/**
 * Edit Record administration screen integration.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Edit Record administration screen.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_Screen_EditRecord extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'load-post.php',                     array( $this, 'load_screen' ) );
		add_action( 'load-post-new.php',                 array( $this, 'load_screen' ) );
		add_action( 'add_meta_boxes_bandstand_record',   array( $this, 'register_meta_boxes' ) );
		add_action( 'bandstand_record_details_meta_box', array( $this, 'display_artist_field' ), 20 );
		add_action( 'bandstand_record_details_meta_box', array( $this, 'display_released_field' ), 40 );
		add_action( 'bandstand_record_details_meta_box', array( $this, 'display_label_field' ), 60 );
		add_action( 'bandstand_record_details_meta_box', array( $this, 'display_catalog_number_field' ), 80 );
		add_action( 'save_post_bandstand_record',        array( $this, 'on_record_save' ), 10, 2 );
	}

	/**
	 * Set up the screen.
	 *
	 * @since 1.0.0
	 */
	public function load_screen() {
		if ( 'bandstand_record' !== get_current_screen()->id ) {
			return;
		}

		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_assets' ) );
		add_action( 'edit_form_after_editor', array( $this, 'display_tracklist' ) );
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
			'bandstand-record-details',
			esc_html__( 'Record Details', 'bandstand' ),
			array( $this, 'display_details_meta_box' ),
			'bandstand_record',
			'side',
			'default'
		);

		add_meta_box(
			'bandstand-record-links',
			esc_html__( 'Record Links', 'bandstand' ),
			array( $this, 'display_links_meta_box' ),
			'bandstand_record',
			'normal',
			'default'
		);
	}

	/**
	 * Enqueue assets for the Edit Record screen.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		$post = get_post();
		$post_type_object = get_post_type_object( 'bandstand_track' );

		wp_enqueue_script(
			'bandstand-record-edit',
			$this->plugin->get_url( 'admin/js/record-edit.bundle.min.js' ),
			array( 'bandstand-admin', 'wp-api', 'wp-backbone', 'wp-util' ),
			'1.0.0',
			true
		);

		$record = bandstand_rest_request( 'GET', '/bandstand/v1/records/' . $post->ID, array(
			'context' => 'edit',
		) );

		if ( 'auto-draft' === $record['status'] ) {
			$record['title'] = '';
		}

		$tracks = bandstand_rest_request( 'GET', '/bandstand/v1/tracks', array(
			'record'  => $post->ID,
			'context' => 'edit',
		) );

		$settings = array(
			'autoNumberTracklist' => 'yes' === $post->bandstand_autonumber_tracklist ? true : false,
			'autoNumberNonce'     => wp_create_nonce( 'update-autonumber_' . $post->ID ),
			'record'              => $record,
			'restUrl'             => esc_url_raw( get_rest_url() ),
			'tracks'              => $tracks,
			'l10n'                => array(
				'addNewTrack'            => $post_type_object->labels->add_new_item,
				'addTrack'               => esc_html__( 'Add a Track', 'bandstand' ),
				'addTrackTitle'          => esc_html__( 'Add a title...', 'bandstand' ),
				'autoNumber'             => esc_html__( 'Auto-number', 'bandstand' ),
				'cancel'                 => esc_html__( 'Cancel', 'bandstand' ),
				'edit'                   => esc_html__( 'Edit', 'bandstand' ),
				'importFromMedia'        => esc_html__( 'Import from Media', 'bandstand' ),
				'importFromMediaLibrary' => esc_html__( 'Import from Media Library', 'bandstand' ),
				'importFromService'      => esc_html__( 'Import from Service', 'bandstand' ),
				'importRecord'           => esc_html__( 'Import Record', 'bandstand' ),
				'manageTracklist'        => esc_html__( 'Manage Tracklist', 'bandstand' ),
				'saveTracklist'          => esc_html__( 'Save Tracklist', 'bandstand' ),
				'select'                 => esc_html__( 'Select', 'bandstand' ),
				'selectFile'             => esc_html__( 'Select File', 'bandstand' ),
				'tracklist'              => esc_html__( 'Tracklist', 'bandstand' ),
				'view'                   => esc_html__( 'View', 'bandstand' ),
			),
		);

		wp_localize_script( 'bandstand-record-edit', '_bandstandTracklistSettings', $settings );
	}

	/**
	 * Tracklist editor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_tracklist( $post ) {
		require( $this->plugin->get_path( 'admin/views/edit-record-tracklist.php' ) );
		require( $this->plugin->get_path( 'admin/views/templates-record.php' ) );
	}

	/**
	 * Display the record details meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The record post object being edited.
	 */
	public function display_details_meta_box( $post ) {
		wp_nonce_field( 'update-record_' . $post->ID, 'bandstand_record_nonce' );
		do_action( 'bandstand_record_details_meta_box', $post );
	}

	/**
	 * Display the record links meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The record post object being edited.
	 */
	public function display_links_meta_box( $post ) {
		$record_links = array_filter( (array) get_post_meta( $post->ID, 'bandstand_record_links', true ) );

		if ( empty( $record_links ) ) {
			$record_links = array( '' );
		}

		require( $this->plugin->get_path( 'admin/views/edit-record-links.php' ) );
	}

	/**
	 * Display a field to edit the record artist.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_artist_field( $post ) {
		?>
		<p class="bandstand-field">
			<label for="record-artist"><?php esc_html_e( 'Artist', 'bandstand' ); ?></label>
			<input type="text" name="bandstand_record[artist]" id="record-artist" value="<?php echo esc_attr( $post->bandstand_artist ); ?>" class="widefat">
		</p>
		<?php
	}

	/**
	 * Display a field to edit the record release date.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_released_field( $post ) {
		$released = preg_replace( '/([0-9]{4})-00-00/', '$1', $post->bandstand_release_date );
		?>
		<p class="bandstand-field">
			<label for="record-release-date"><?php esc_html_e( 'Release Date', 'bandstand' ); ?></label>
			<input type="text" name="bandstand_record[release_date]" id="record-release-date" value="<?php echo esc_attr( $released ); ?>" placeholder="YYYY-MM-DD" autocomplete="off" class="widefat">
		</p>
		<?php
	}

	/**
	 * Display a field to edit the record label.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_label_field( $post ) {
		?>
		<p class="bandstand-field">
			<label for="record-label"><?php esc_html_e( 'Record Label', 'bandstand' ); ?></label>
			<input type="text" name="bandstand_record[label]" id="record-label" value="<?php echo esc_attr( $post->bandstand_label ); ?>" class="widefat">
		</p>
		<?php
	}

	/**
	 * Display a field to edit the catalog number.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_catalog_number_field( $post ) {
		?>
		<p class="bandstand-field">
			<label for="catalog-number"><?php esc_html_e( 'Catalog Number', 'bandstand' ); ?></label>
			<input type="text" name="bandstand_record[catalog_number]" id="catalog-number" value="<?php echo esc_attr( $post->bandstand_catalog_number ); ?>" class="widefat">
		</p>
		<?php
	}

	/**
	 * Process and save record info when the CPT is saved.
	 *
	 * Creates and updates child tracks and saves additional record meta.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 */
	public function on_record_save( $post_id ) {
		static $is_active = false; // Prevent recursion.

		$is_autosave    = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = isset( $_POST['bandstand_record_nonce'] ) && wp_verify_nonce( $_POST['bandstand_record_nonce'], 'update-record_' . $post_id );

		// Bail if the data shouldn't be saved or intention can't be verified.
		if ( $is_active || $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		$is_active = true;

		$data = array_merge( $_POST['bandstand_record'], array( 'ID' => $post_id ) );
		save_bandstand_record( $data )->update_track_count();

		$is_active = false;
	}
}
