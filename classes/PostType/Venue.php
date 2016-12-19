<?php
/**
 * Venue post type registration and integration.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class for registering the venue post type and integration.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_PostType_Venue extends Bandstand_PostType_AbstractPostType {
	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $post_type = 'bandstand_venue';

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'init',                  array( $this, 'register_post_type' ) );
		add_action( 'init',                  array( $this, 'register_meta' ) );
		add_filter( 'wp_insert_post_data',   array( $this, 'add_uuid_to_new_posts' ) );
		add_action( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
	}

	/**
	 * Register post meta.
	 *
	 * @since 1.0.0
	 */
	public function register_meta() {
		register_meta( 'post', 'bandstand_address', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_city', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_contact_email', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_contact_name', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_contact_phone', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_country', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_gig_count', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'absint',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'integer',
		) );

		register_meta( 'post', 'bandstand_latitude', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_longitude', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_notes', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_phone', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_postal_code', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_region', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_timezone_id', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_website_url', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'esc_url_raw',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );
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
			'has_archive'        => false,
			'hierarchical'       => false,
			'labels'             => $this->get_labels(),
			'public'             => false,
			'publicly_queryable' => false,
			'query_var'          => 'bandstand_venue',
			'rewrite'            => false,
			'show_in_menu'       => 'edit.php?post_type=bandstand_gig',
			'show_ui'            => true,
			'supports'           => array( 'title' ),
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
			'name'                  => esc_html_x( 'Venues', 'post type general name', 'bandstand' ),
			'singular_name'         => esc_html_x( 'Venue', 'post type singular name', 'bandstand' ),
			'add_new'               => esc_html_x( 'Add New', 'venue', 'bandstand' ),
			'add_new_item'          => esc_html__( 'Add New Venue', 'bandstand' ),
			'edit_item'             => esc_html__( 'Edit Venue', 'bandstand' ),
			'new_item'              => esc_html__( 'New Venue', 'bandstand' ),
			'view_item'             => esc_html__( 'View Venue', 'bandstand' ),
			'search_items'          => esc_html__( 'Search Venues', 'bandstand' ),
			'not_found'             => esc_html__( 'No venues found', 'bandstand' ),
			'not_found_in_trash'    => esc_html__( 'No venues found in Trash', 'bandstand' ),
			'parent_item_colon'     => esc_html__( 'Parent Venue:', 'bandstand' ),
			'all_items'             => esc_html__( 'Venues', 'bandstand' ),
			'menu_name'             => esc_html_x( 'Venues', 'admin menu name', 'bandstand' ),
			'name_admin_bar'        => esc_html_x( 'Venue', 'add new on admin bar', 'bandstand' ),
			'insert_into_item'      => esc_html__( 'Insert into venue', 'bandstand' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this venue', 'bandstand' ),
			'filter_items_list'     => esc_html__( 'Filter venues list', 'bandstand' ),
			'items_list_navigation' => esc_html__( 'Venues list navigation', 'bandstand' ),
			'items_list'            => esc_html__( 'Venues list', 'bandstand' ),
		);
	}

	/**
	 * Retrieve post updated messages.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_Post $post Post object.
	 * @return array
	 */
	protected function get_updated_messages( $post ) {
		return array(
			0  => '', // Unused. Messages start at index 1.
			1  => esc_html__( 'Venue updated.', 'bandstand' ),
			2  => esc_html__( 'Custom field updated.', 'bandstand' ),
			3  => esc_html__( 'Custom field deleted.', 'bandstand' ),
			4  => esc_html__( 'Venue updated.', 'bandstand' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Venue restored to revision from %s.', 'bandstand' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => esc_html__( 'Venue published.', 'bandstand' ),
			7  => esc_html__( 'Venue saved.', 'bandstand' ),
			8  => esc_html__( 'Venue submitted.', 'bandstand' ),
			9  => sprintf(
				esc_html__( 'Venue scheduled for: %s.', 'bandstand' ),
				/* translators: Publish box date format, see http://php.net/date */
				'<strong>' . date_i18n( esc_html__( 'M j, Y @ H:i', 'bandstand' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => esc_html__( 'Venue draft updated.', 'bandstand' ),
			'preview' => esc_html__( 'Preview venue', 'bandstand' ),
			'view'    => esc_html__( 'View venue', 'bandstand' ),
		);
	}
}
