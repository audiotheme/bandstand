<?php
/**
 * Video post type registration and integration.
 *
 * @package   Bandstand\Videos
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class for registering the video post type and integration.
 *
 * @package Bandstand\Videos
 * @since   1.0.0
 */
class Bandstand_PostType_Video extends Bandstand_PostType_AbstractPostType {
	/**
	 * Videos module.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Module_Videos
	 */
	protected $module;

	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $post_type = 'bandstand_video';

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @param Bandstand_Module_Videos $module Artists module.
	 */
	public function __construct( Bandstand_Module_Videos $module ) {
		$this->module = $module;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'init',                  array( $this, 'register_post_type' ) );
		add_action( 'init',                  array( $this, 'register_meta' ) );
		add_action( 'pre_get_posts',         array( $this, 'sort_query' ) );
		add_action( 'delete_attachment',     array( $this, 'delete_oembed_thumbnail_data' ) );
		add_filter( 'wp_insert_post_data',   array( $this, 'add_uuid_to_new_posts' ) );
		add_action( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
	}

	/**
	 * Register post meta.
	 *
	 * @since 1.0.0
	 */
	public function register_meta() {
		register_meta( 'post', 'bandstand_duration', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_video_url', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'esc_url_raw',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );
	}

	/**
	 * Sort video archive requests.
	 *
	 * Defaults to sorting by publish date in descending order. A plugin can
	 * hook into pre_get_posts at an earlier priority and manually set the order.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query The main WP_Query object. Passed by reference.
	 */
	public function sort_query( $wp_query ) {
		if ( is_admin() || ! $wp_query->is_main_query() || ! $this->is_archive_request() ) {
			return;
		}

		$orderby = $wp_query->get( 'orderby' );
		if ( $orderby ) {
			return;
		}

		$orderby = get_bandstand_archive_meta( 'orderby', true, 'post_date', 'bandstand_video' );
		$order   = get_bandstand_archive_meta( 'order', true, '', 'bandstand_video' );

		switch ( $orderby ) {
			// Use a plugin like Simple Page Ordering to change the menu order.
			case 'custom' :
				$wp_query->set( 'orderby', 'menu_order' );
				$wp_query->set( 'order', 'asc' );
				break;

			case 'title' :
				$wp_query->set( 'orderby', 'title' );
				$wp_query->set( 'order', empty( $order ) ? 'asc' : $order );
				break;

			// Sort videos by publish date.
			default :
				$wp_query->set( 'orderby', 'post_date' );
				$wp_query->set( 'order', empty( $order ) ? 'desc' : $order );
		}
	}

	/**
	 * Delete oEmbed thumbnail post meta if the associated attachment is deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param int $attachment_id The ID of the attachment being deleted.
	 */
	public function delete_oembed_thumbnail_data( $attachment_id ) {
		global $wpdb;

		$sql = $wpdb->prepare(
			"SELECT post_id
			FROM $wpdb->postmeta
			WHERE meta_key = '_bandstand_oembed_thumbnail_id' AND meta_value = %d",
			$attachment_id
		);

		$post_id = $wpdb->get_var( $sql );

		if ( $post_id ) {
			delete_post_meta( $post_id, '_bandstand_oembed_thumbnail_id' );
			delete_post_meta( $post_id, '_bandstand_oembed_thumbnail_url' );
		}
	}

	/**
	 * Retrieve post type registration argments.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_args() {
		return array(
			'has_archive'        => $this->module->get_rewrite_base(),
			'hierarchical'       => true,
			'labels'             => $this->get_labels(),
			'menu_icon'          => bandstand_encode_svg( 'admin/images/dashicons/videos.svg' ),
			'menu_position'      => 514,
			'public'             => true,
			'publicly_queryable' => true,
			'rewrite'            => array(
				'slug'       => $this->module->get_rewrite_base(),
				'with_front' => false,
			),
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'author', 'custom-fields' ),
		);
	}

	/**
	 * Retrieve post type labels.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_labels() {
		return array(
			'name'                  => esc_html_x( 'Videos', 'post type general name', 'bandstand' ),
			'singular_name'         => esc_html_x( 'Video', 'post type singular name', 'bandstand' ),
			'add_new'               => esc_html_x( 'Add New', 'video', 'bandstand' ),
			'add_new_item'          => esc_html__( 'Add New Video', 'bandstand' ),
			'edit_item'             => esc_html__( 'Edit Video', 'bandstand' ),
			'new_item'              => esc_html__( 'New Video', 'bandstand' ),
			'view_item'             => esc_html__( 'View Video', 'bandstand' ),
			'search_items'          => esc_html__( 'Search Videos', 'bandstand' ),
			'not_found'             => esc_html__( 'No videos found', 'bandstand' ),
			'not_found_in_trash'    => esc_html__( 'No videos found in Trash', 'bandstand' ),
			'parent_item_colon'     => esc_html__( 'Parent Video:', 'bandstand' ),
			'all_items'             => esc_html__( 'All Videos', 'bandstand' ),
			'menu_name'             => esc_html_x( 'Videos', 'admin menu name', 'bandstand' ),
			'name_admin_bar'        => esc_html_x( 'Video', 'add new on admin bar', 'bandstand' ),
			'insert_into_item'      => esc_html__( 'Insert into video', 'bandstand' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this video', 'bandstand' ),
			'filter_items_list'     => esc_html__( 'Filter videos list', 'bandstand' ),
			'items_list_navigation' => esc_html__( 'Videos list navigation', 'bandstand' ),
			'items_list'            => esc_html__( 'Videos list', 'bandstand' ),
		);
	}

	/**
	 * Retrieve post updated messages.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	protected function get_updated_messages( $post ) {
		return array(
			0  => '', // Unused. Messages start at index 1.
			1  => esc_html__( 'Video updated.', 'bandstand' ),
			2  => esc_html__( 'Custom field updated.', 'bandstand' ),
			3  => esc_html__( 'Custom field deleted.', 'bandstand' ),
			4  => esc_html__( 'Video updated.', 'bandstand' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Video restored to revision from %s.', 'bandstand' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => esc_html__( 'Video published.', 'bandstand' ),
			7  => esc_html__( 'Video saved.', 'bandstand' ),
			8  => esc_html__( 'Video submitted.', 'bandstand' ),
			9  => sprintf(
				esc_html__( 'Video scheduled for: %s.', 'bandstand' ),
				/* translators: Publish box date format, see http://php.net/date */
				'<strong>' . date_i18n( esc_html__( 'M j, Y @ H:i', 'bandstand' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => esc_html__( 'Video draft updated.', 'bandstand' ),
			'preview' => esc_html__( 'Preview video', 'bandstand' ),
			'view'    => esc_html__( 'View video', 'bandstand' ),
		);
	}

	/**
	 * Whether the current request is for a video archive.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_archive_request() {
		return is_post_type_archive( $this->post_type ) || is_tax( 'bandstand_video_category' );
	}
}
