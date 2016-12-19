<?php
/**
 * Edit Video administration screen integration.
 *
 * @package   Bandstand\Videos
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class providing integration with the Edit Video administration screen.
 *
 * @package Bandstand\Videos
 * @since   1.0.0
 */
class Bandstand_Screen_EditVideo extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'load-post.php',                  array( $this, 'load_screen' ) );
		add_action( 'load-post-new.php',              array( $this, 'load_screen' ) );
		add_action( 'admin_enqueue_scripts',          array( $this, 'register_assets' ), 1 );
		add_action( 'add_meta_boxes_bandstand_video', array( $this, 'register_meta_boxes' ) );
		add_filter( 'admin_post_thumbnail_html',      array( $this, 'admin_post_thumbnail_html' ), 10, 3 );
		add_action( 'save_post_bandstand_video',      array( $this, 'on_video_save' ), 10, 2 );
	}

	/**
	 * Set up the screen.
	 *
	 * @since 1.0.0
	 */
	public function load_screen() {
		if ( 'bandstand_video' !== get_current_screen()->id ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'edit_form_after_title', array( $this, 'display_video_url_field' ) );
	}

	/**
	 * Register video meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The video post object being edited.
	 */
	public function register_meta_boxes( $post ) {
		add_meta_box(
			'bandstand-video-details',
			esc_html__( 'Details', 'bandstand' ),
			array( $this, 'display_details_meta_box' ),
			'bandstand_video',
			'side',
			'default'
		);
	}

	/**
	 * Register scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function register_assets() {
		wp_register_script(
			'bandstand-video-edit',
			$this->plugin->get_url( 'admin/js/video-edit.js' ),
			array( 'jquery', 'post', 'wp-backbone', 'wp-util' ),
			'1.9.0',
			true
		);
	}

	/**
	 * Enqueue assets for the Edit Venue screen.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'bandstand-video-edit' );
	}

	/**
	 * Display a field to enter a video URL after the post title.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_video_url_field( $post ) {
		wp_nonce_field( 'update-video_' . $post->ID, 'bandstand_video_nonce', false );
		?>
		<div class="bandstand-edit-after-title" style="position: relative">
			<p>
				<label for="bandstand-video-url" class="screen-reader-text"><?php esc_html_e( 'Video URL:', 'bandstand' ); ?></label>
				<input type="text" name="bandstand_video[video_url]" id="bandstand-video-url" value="<?php echo esc_url( $post->bandstand_video_url ); ?>" placeholder="<?php esc_attr_e( 'Video URL', 'bandstand' ); ?>" class="widefat"><br>

				<span class="description">
					<?php
					printf(
						wp_kses(
							__( 'Enter a video URL from a <a href="%s" target="_blank">supported video service</a>.', 'bandstand' ),
							array( 'a' => array( 'href' => true, 'target' => true ) )
						),
						'https://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F'
					);
					?>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Display video details meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The video post object being edited.
	 */
	public function display_details_meta_box( $post ) {
		?>
		<p class="bandstand-field">
			<label for="video-duration"><?php esc_html_e( 'Duration', 'bandstand' ); ?></label>
			<input type="text" name="bandstand_video[duration]" id="video-duration" value="<?php echo esc_attr( $post->bandstand_duration ); ?>" placeholder="00:00" class="widefat">
		</p>
		<?php
	}

	/**
	 * Add a link to get the video thumbnail from an oEmbed endpoint.
	 *
	 * Adds data about the current thumbnail and a previously fetched thumbnail
	 * from an oEmbed endpoint so the link can be hidden or shown as necessary. A
	 * function is also fired each time the HTML is output in order to determine
	 * whether the link should be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Default post thumbnail HTML.
	 * @param int    $post_id Post ID.
	 * @return string
	 */
	public function admin_post_thumbnail_html( $content, $post_id, $thumbnail_id ) {
		if ( 'bandstand_video' !== get_post_type( $post_id ) ) {
			return $content;
		}

		$data = array(
			'thumbnailId'       => $thumbnail_id,
			'oembedThumbnailId' => get_post_meta( $post_id, '_bandstand_oembed_thumbnail_id', true ),
		);

		ob_start();
		?>
		<p id="bandstand-select-oembed-thumb" class="hide-if-no-js">
			<a href="#" id="bandstand-select-oembed-thumb-button"><?php esc_html_e( 'Fetch video thumbnail', 'bandstand' ); ?></a>
			<span class="spinner"></span>
		</p>
		<script id="bandstand-video-thumbnail-data" type="application/json"><?php echo wp_json_encode( $data ); ?></script>
		<script>if ( '_bandstandVideoThumbnailPing' in window ) { _bandstandVideoThumbnailPing(); }</script>
		<?php
		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Process and save video info when the CPT is saved.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Video post ID.
	 * @param WP_Post $post Video post object.
	 */
	public function on_video_save( $post_id, $post ) {
		static $is_active = false; // Prevent recursion.

		$is_autosave    = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = isset( $_POST['bandstand_video_nonce'] ) && wp_verify_nonce( $_POST['bandstand_video_nonce'], 'update-video_' . $post_id );

		// Bail if the data shouldn't be saved or intention can't be verified.
		if ( $is_active || $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		$is_active = true;

		$data = array_merge( $_POST['bandstand_video'], array( 'ID' => $post_id ) );
		save_bandstand_video( $data );

		$is_active = false;
	}
}
