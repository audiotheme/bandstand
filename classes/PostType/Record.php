<?php
/**
 * Record post type registration and integration.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class for registering the record post type and integration.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_PostType_Record extends Bandstand_PostType_AbstractPostType {
	/**
	 * Discography module.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Module_Discography
	 */
	protected $module;

	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $post_type = 'bandstand_record';

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @param Bandstand_Module_Discography $module Gigs module.
	 */
	public function __construct( Bandstand_Module_Discography $module ) {
		$this->module = $module;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'init',                   array( $this, 'register_post_type' ) );
		add_action( 'init',                   array( $this, 'register_meta' ) );
		add_action( 'pre_get_posts',          array( $this, 'sort_query' ) );
		add_filter( 'post_type_archive_link', array( $this, 'archive_permalink' ), 10, 2 );
		add_filter( 'post_type_link',         array( $this, 'post_permalink' ), 10, 4 );
		add_filter( 'wp_insert_post_data',    array( $this, 'add_uuid_to_new_posts' ) );
		add_action( 'post_updated_messages',  array( $this, 'post_updated_messages' ) );
	}

	/**
	 * Register post meta.
	 *
	 * @since 1.0.0
	 */
	public function register_meta() {
		register_meta( 'post', 'bandstand_artist', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_catalog_number', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_label', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_record_links', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => array( $this, 'sanitize_record_links' ),
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'array',
		) );

		register_meta( 'post', 'bandstand_release_date', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => array( $this, 'sanitize_release_date' ),
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_track_count', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'absint',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'integer',
		) );
	}

	/**
	 * Sort record archive requests.
	 *
	 * Defaults to sorting by release year in descending order. An option is
	 * available on the archive page to sort by title or a custom order. The custom
	 * order using the 'menu_order' value, which can be set using a plugin like
	 * Simple Page Ordering.
	 *
	 * Alternatively, a plugin can hook into pre_get_posts at an earlier priority
	 * and manually set the order.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query The main WP_Query object. Passed by reference.
	 */
	public function sort_query( $wp_query ) {
		if (
			is_admin() ||
			! $wp_query->is_main_query() ||
			! $this->is_archive_request() ||
			$wp_query->get( 'orderby' )
		) {
			return;
		}

		$orderby = get_bandstand_archive_meta( 'orderby', true, 'release_date', 'bandstand_record' );
		$order   = get_bandstand_archive_meta( 'order', true, '', 'bandstand_record' );

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

			// Sort records by release date, then by title.
			default :
				$wp_query->set( 'meta_key', 'bandstand_release_date' );
				$wp_query->set( 'orderby', 'meta_value' );
				$wp_query->set( 'order', empty( $order ) ? 'desc' : $order );
				add_filter( 'posts_orderby_request', array( $this, 'sort_query_sql' ) );
		}

		do_action_ref_array( 'bandstand_record_query_sort', array( &$wp_query ) );
	}

	/**
	 * Sort records by title after sorting by release year.
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb
	 *
	 * @param string $orderby SQL order clause.
	 * @return string
	 */
	public function sort_query_sql( $orderby ) {
		global $wpdb;
		return $orderby . ", {$wpdb->posts}.post_title ASC";
	}

	/**
	 * Filter the permalink for the discography archive.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link The default archive URL.
	 * @param string $post_type Post type.
	 * @return string The discography archive URL.
	 */
	public function archive_permalink( $link, $post_type ) {
		$permalink = get_option( 'permalink_structure' );
		if ( ! empty( $permalink ) && 'bandstand_record' === $post_type ) {
			$link = home_url( '/' . $this->module->get_rewrite_base() . '/' );
		}

		return $link;
	}

	/**
	 * Filter record permalinks to match the custom rewrite rules.
	 *
	 * Allows the standard WordPress API function get_permalink() to return the
	 * correct URL when used with a record post type.
	 *
	 * @since 1.0.0
	 *
	 * @see get_post_permalink()
	 *
	 * @param string $post_link The default permalink.
	 * @param object $post The record post object to get the permalink for.
	 * @param bool   $leavename Whether to keep the post name.
	 * @param bool   $sample Is it a sample permalink.
	 * @return string
	 */
	public function post_permalink( $post_link, $post, $leavename, $sample ) {
		if ( $this->is_draft_or_pending( $post ) || 'bandstand_record' !== get_post_type( $post ) ) {
			return $post_link;
		}

		if ( get_option( 'permalink_structure' ) ) {
			$base      = $this->module->get_rewrite_base();
			$slug      = $leavename ? '%postname%' : $post->post_name;
			$post_link = home_url( sprintf( '/%s/%s/', $base, $slug ) );
		}

		return $post_link;
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
			'menu_icon'          => bandstand_encode_svg( 'admin/images/dashicons/discography.svg' ),
			'menu_position'      => 513,
			'public'             => true,
			'publicly_queryable' => true,
			'rewrite'            => false,
			'show_ui'            => true,
			'show_in_admin_bar'  => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		);
	}

	/**
	 * Retrieve post type labels.
	 *
	 * @return array
	 */
	protected function get_labels() {
		return array(
			'name'                  => esc_html_x( 'Records', 'post type general name', 'bandstand' ),
			'singular_name'         => esc_html_x( 'Record', 'post type singular name', 'bandstand' ),
			'add_new'               => esc_html_x( 'Add New', 'record', 'bandstand' ),
			'add_new_item'          => esc_html__( 'Add New Record', 'bandstand' ),
			'edit_item'             => esc_html__( 'Edit Record', 'bandstand' ),
			'new_item'              => esc_html__( 'New Record', 'bandstand' ),
			'view_item'             => esc_html__( 'View Record', 'bandstand' ),
			'search_items'          => esc_html__( 'Search Records', 'bandstand' ),
			'not_found'             => esc_html__( 'No records found', 'bandstand' ),
			'not_found_in_trash'    => esc_html__( 'No records found in Trash', 'bandstand' ),
			'parent_item_colon'     => esc_html__( 'Parent Record:', 'bandstand' ),
			'all_items'             => esc_html__( 'All Records', 'bandstand' ),
			'menu_name'             => esc_html_x( 'Discography', 'admin menu name', 'bandstand' ),
			'name_admin_bar'        => esc_html_x( 'Record', 'add new on admin bar', 'bandstand' ),
			'insert_into_item'      => esc_html__( 'Insert into record', 'bandstand' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this record', 'bandstand' ),
			'filter_items_list'     => esc_html__( 'Filter records list', 'bandstand' ),
			'items_list_navigation' => esc_html__( 'Records list navigation', 'bandstand' ),
			'items_list'            => esc_html__( 'Records list', 'bandstand' ),
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
			1  => esc_html__( 'Record updated.', 'bandstand' ),
			2  => esc_html__( 'Custom field updated.', 'bandstand' ),
			3  => esc_html__( 'Custom field deleted.', 'bandstand' ),
			4  => esc_html__( 'Record updated.', 'bandstand' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Record restored to revision from %s.', 'bandstand' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => esc_html__( 'Record published.', 'bandstand' ),
			7  => esc_html__( 'Record saved.', 'bandstand' ),
			8  => esc_html__( 'Record submitted.', 'bandstand' ),
			9  => sprintf(
				esc_html__( 'Record scheduled for: %s.', 'bandstand' ),
				/* translators: Publish box date format, see http://php.net/date */
				'<strong>' . date_i18n( esc_html__( 'M j, Y @ H:i', 'bandstand' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => esc_html__( 'Record draft updated.', 'bandstand' ),
			'preview' => esc_html__( 'Preview record', 'bandstand' ),
			'view'    => esc_html__( 'View record', 'bandstand' ),
		);
	}

	/**
	 * Whether the current request is for a record archive.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_archive_request() {
		return is_post_type_archive( $this->post_type ) || is_tax( 'bandstand_record_type' );
	}

	/**
	 * Sanitize record links.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $value Array of link names and URLs.
	 * @return array
	 */
	public function sanitize_record_links( $value ) {
		$links = array();

		if ( empty( $value ) || ! is_array( $value ) ) {
			return $links;
		}

		foreach ( $value as $link ) {
			if ( empty( $link['name'] ) || empty( $link['url'] ) ) {
				continue;
			}

			$links[] = array(
				'name' => sanitize_text_field( $link['name'] ),
				'url'  => esc_url_raw( $link['url'] ),
			);
		}

		return $links;
	}

	/**
	 * Sanitize release date.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value Date.
	 * @return string
	 */
	public function sanitize_release_date( $value ) {
		$parsed = date_parse( $value );

		$date = '';
		if ( checkdate( $parsed['month'], $parsed['day'], $parsed['year'] ) ) {
			$date = sprintf(
				'%d-%s-%s',
				$parsed['year'],
				zeroise( $parsed['month'], 2 ),
				zeroise( $parsed['day'], 2 )
			);
		} elseif ( preg_match( '/[1-9][0-9]{3}/', $value ) ) {
			$date = sprintf( '%d-00-00', $value );
		}

		return $date;
	}
}
