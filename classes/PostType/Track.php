<?php
/**
 * Track post type registration and integration.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class for registering the track post type and integration.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_PostType_Track extends Bandstand_PostType_AbstractPostType {
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
	protected $post_type = 'bandstand_track';

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
		add_action( 'init',                      array( $this, 'register_post_type' ) );
		add_action( 'init',                      array( $this, 'register_meta' ) );
		add_action( 'pre_get_posts',             array( $this, 'track_query' ) );
		add_filter( 'post_type_archive_link',    array( $this, 'archive_permalink' ), 10, 2 );
		add_filter( 'post_type_link',            array( $this, 'post_permalink' ), 10, 4 );
		add_filter( 'wp_unique_post_slug',       array( $this, 'get_unique_slug' ), 10, 6 );
		add_filter( 'get_post_metadata',         array( $this, 'filter_post_thumbnail_id' ), 10, 4 );
		add_action( 'save_post-bandstand_track', array( $this, 'on_create' ), 10, 3 );
		add_action( 'before_delete_post',        array( $this, 'on_before_delete' ) );
		add_filter( 'wp_insert_post_data',       array( $this, 'add_uuid_to_new_posts' ) );
		add_filter( 'post_updated_messages',     array( $this, 'post_updated_messages' ) );
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

		register_meta( 'post', 'bandstand_download_url', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'esc_url_raw',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_duration', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => array( $this, 'sanitize_duration' ),
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_purchase_url', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'esc_url_raw',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_stream_url', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'esc_url_raw',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );

		register_meta( 'post', 'bandstand_track_number', array(
			'auth_callback'     => '__return_false',
			'description'       => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		) );
	}

	/**
	 * Filter track requests.
	 *
	 * Tracks must belong to a record, so the parent record is set for track
	 * requests.
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb
	 *
	 * @param WP_Query $wp_query The main WP_Query object. Passed by reference.
	 */
	public function track_query( $wp_query ) {
		global $wpdb;

		if ( is_admin() || ! $wp_query->is_main_query() ) {
			return;
		}

		if ( ! is_single() || 'bandstand_track' !== $wp_query->get( 'post_type' ) ) {
			return;
		}

		// Limit requests for single tracks to the context of the parent record.
		if ( get_option( 'permalink_structure' ) ) {
			$record_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID
				FROM $wpdb->posts
				WHERE post_type = 'bandstand_record' AND post_name = %s
				LIMIT 1",
				$wp_query->get( 'bandstand_record' )
			) );
		} elseif ( ! empty( $_GET['post_parent'] ) ) {
			$record_id = absint( $_GET['post_parent'] );
		}

		if ( ! empty( $record_id ) ) {
			$wp_query->set( 'post_parent', $record_id );
		}
	}

	/**
	 * Filter the permalink for track archives.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link The default archive URL.
	 * @param string $post_type Post type.
	 * @return string The discography archive URL.
	 */
	public function archive_permalink( $link, $post_type ) {
		$permalink = get_option( 'permalink_structure' );
		if ( ! empty( $permalink ) && 'bandstand_track' === $post_type ) {
			$link = home_url( '/' . $this->module->get_rewrite_base() . '/' );
		}

		return $link;
	}

	/**
	 * Filter track permalinks to match the custom rewrite rules.
	 *
	 * Allows the standard WordPress API function get_permalink() to return the
	 * correct URL when used with a track post type.
	 *
	 * @since 1.0.0
	 *
	 * @see get_post_permalink()
	 *
	 * @param string  $post_link The default permalink.
	 * @param WP_Post $post The track post object to get the permalink for.
	 * @param bool    $leavename Whether to keep the post name.
	 * @param bool    $sample Is it a sample permalink.
	 * @return string
	 */
	public function post_permalink( $post_link, $post, $leavename, $sample ) {
		if ( $this->is_draft_or_pending( $post ) || 'bandstand_track' !== get_post_type( $post ) ) {
			return $post_link;
		}

		$permalink = get_option( 'permalink_structure' );

		if ( ! empty( $permalink ) && ! empty( $post->post_parent ) ) {
			$base   = $this->module->get_rewrite_base();
			$slug   = $leavename ? '%postname%' : $post->post_name;
			$record = get_post( $post->post_parent );

			if ( $record ) {
				$post_link = home_url( sprintf( '/%s/%s/track/%s/', $base, $record->post_name, $slug ) );
			}
		} elseif ( empty( $permalink ) && ! empty( $post->post_parent ) ) {
			$post_link = add_query_arg( 'post_parent', $post->post_parent, $post_link );
		}

		return $post_link;
	}

	/**
	 * Ensure track slugs are unique.
	 *
	 * Tracks should always be associated with a record so their slugs only need
	 * to be unique within the context of a record.
	 *
	 * @since 1.0.0
	 *
	 * @see wp_unique_post_slug()
	 * @global wpdb $wpdb
	 * @global WP_Rewrite $wp_rewrite
	 *
	 * @param string  $slug The desired slug (post_name).
	 * @param integer $post_id Post ID.
	 * @param string  $post_status No uniqueness checks are made if the post is still draft or pending.
	 * @param string  $post_type Post type.
	 * @param integer $post_parent Post parent ID.
	 * @param string  $original_slug Slug passed to the uniqueness method.
	 * @return string
	 */
	public function get_unique_slug( $slug, $post_id, $post_status, $post_type, $post_parent, $original_slug = null ) {
		global $wpdb, $wp_rewrite;

		if ( 'bandstand_track' === $post_type ) {
			$slug = $original_slug;

			$feeds = $wp_rewrite->feeds;
			if ( ! is_array( $feeds ) ) {
				$feeds = array();
			}

			// Make sure the track slug is unique within the context of the record only.
			$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND post_parent = %d AND ID != %d LIMIT 1";
			$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_type, $post_parent, $post_id ) );

			if ( $post_name_check || apply_filters( 'wp_unique_post_slug_is_bad_flat_slug', false, $slug, $post_type ) ) {
				$suffix = 2;
				do {
					$alt_post_name = substr( $slug, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
					$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $post_type, $post_parent, $post_id ) );
					$suffix++;
				} while ( $post_name_check );
				$slug = $alt_post_name;
			}
		}

		return $slug;
	}

	/**
	 * Filter post thumbnail meta for tracks.
	 *
	 * Returns the parent record thumbnail ID if the track doesn't have a post
	 * thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value    The post meta value.
	 * @param int    $post_id  Post ID.
	 * @param string $meta_key Meta key.
	 * @param bool   $single   Whether to return only the first value of the specified $meta_key.
	 * @return int|null
	 */
	public function filter_post_thumbnail_id( $value, $post_id, $meta_key, $single ) {
		if ( ! $single || '_thumbnail_id' !== $meta_key ) {
			return $value;
		}

		$meta_cache = wp_cache_get( $post_id, 'post_meta' );
		if ( ! $meta_cache ) {
			$meta_cache = update_meta_cache( 'post', array( $post_id ) );
			$meta_cache = $meta_cache[ $post_id ];
		}

		if (
			! isset( $meta_cache[ $meta_key ] ) &&
			! empty( $post->post_parent ) &&
			'bandstand_track' === get_post_type( $post_id )
		) {
			return get_post_thumbnail_id( get_post( $post_id )->post_parent );
		}

		return $value;
	}

	/**
	 * Transform a track id or array of data into the expected format for use as
	 * a JavaScript object.
	 *
	 * @since 1.0.0
	 *
	 * @todo Either remove or update to use post models.
	 *
	 * @param int|array $track Track ID or array of expected track properties.
	 * @return array
	 */
	public function prepare_track_for_js( $track ) {
		$data = array(
			'artist'  => '',
			'artwork' => '',
			'mp3'     => '',
			'record'  => '',
			'title'   => '',
		);

		// Enqueue a track post type.
		if ( 'bandstand_track' === get_post_type( $track ) ) {
			$track  = get_bandstand_track( $track );
			$record = $track->get_record();

			$data['artist'] = $track->get_artist();
			$data['mp3']    = $track->get_stream_url();
			$data['record'] = $record->post_title;
			$data['title']  = $track->post_title;

			// WP playlist format.
			$data['format']                   = 'mp3';
			$data['meta']['artist']           = $data['artist'];
			$data['meta']['length_formatted'] = '0:00';
			$data['src']                      = $data['mp3'];

			$thumbnail_id = get_post_thumbnail_id( $track->ID );
			if ( ! empty( $thumbnail_id ) ) {
				$image = wp_get_attachment_image_src( $thumbnail_id, apply_filters( 'bandstand_track_js_artwork_size', 'thumbnail' ) );
				$data['artwork'] = $image[0];
			}
		}

		// Add the track data directly.
		elseif ( is_array( $track ) ) {
			if ( isset( $track['artwork'] ) ) {
				$data['artwork'] = esc_url( $track['artwork'] );
			}

			if ( isset( $track['file'] ) ) {
				$data['mp3'] = esc_url_raw( bandstand_encode_url_path( $track['file'] ) );
			}

			if ( isset( $track['mp3'] ) ) {
				$data['mp3'] = esc_url_raw( bandstand_encode_url_path( $track['mp3'] ) );
			}

			if ( isset( $track['title'] ) ) {
				$data['title'] = wp_strip_all_tags( $track['title'] );
			}

			$data = array_merge( $track, $data );
		}

		$data = apply_filters( 'bandstand_track_js_data', $data, $track );

		return $data;
	}

	/**
	 * Update a record's cached track count when track is created.
	 *
	 * @since 1.0.0
	 *
	 * @todo May need to disable this when importing.
	 *
	 * @param int     $post_id   ID of the track being deleted.
	 * @param WP_Post $post      Post object.
	 * @param boolean $is_update Whether the operation is an update.
	 */
	public function on_create( $post_id, $post, $is_update ) {
		if ( $is_update || empty( $post->post_parent ) ) {
			return;
		}

		$record = get_bandstand_track( $post )->get_record();
		$count = $record->get_track_count();
		$record->update_track_count( ++$count );
	}

	/**
	 * Update a record's cached track count when track is deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id ID of the track being deleted.
	 */
	public function on_before_delete( $post_id ) {
		if ( 'bandstand_track' === get_post_type( $post_id ) ) {
			$track = get_bandstand_track( $post_id );
			if ( $track->post_parent ) {
				$record = $track->get_record();
				$count = $record->get_track_count();
				$record->update_track_count( --$count );
			}
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
			'has_archive'        => false,
			'hierarchical'       => false,
			'labels'             => $this->get_labels(),
			'public'             => true,
			'publicly_queryable' => true,
			'rewrite'            => false,
			'show_ui'            => true,
			'show_in_admin_bar'  => true,
			'show_in_menu'       => 'edit.php?post_type=bandstand_record',
			'show_in_nav_menus'  => true,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		);
	}

	/**
	 * Retrieve post type labels.
	 *
	 * @return array
	 */
	protected function get_labels() {
		return array(
			'name'                  => esc_html_x( 'Tracks', 'post type general name', 'bandstand' ),
			'singular_name'         => esc_html_x( 'Track', 'post type singular name', 'bandstand' ),
			'add_new'               => esc_html_x( 'Add New', 'track', 'bandstand' ),
			'add_new_item'          => esc_html__( 'Add New Track', 'bandstand' ),
			'edit_item'             => esc_html__( 'Edit Track', 'bandstand' ),
			'new_item'              => esc_html__( 'New Track', 'bandstand' ),
			'view_item'             => esc_html__( 'View Track', 'bandstand' ),
			'search_items'          => esc_html__( 'Search Tracks', 'bandstand' ),
			'not_found'             => esc_html__( 'No tracks found', 'bandstand' ),
			'not_found_in_trash'    => esc_html__( 'No tracks found in Trash', 'bandstand' ),
			'parent_item_colon'     => esc_html__( 'Parent Track:', 'bandstand' ),
			'all_items'             => esc_html__( 'All Tracks', 'bandstand' ),
			'menu_name'             => esc_html_x( 'Tracks', 'admin menu name', 'bandstand' ),
			'name_admin_bar'        => esc_html_x( 'Track', 'add new on admin bar', 'bandstand' ),
			'insert_into_item'      => esc_html__( 'Insert into track', 'bandstand' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this track', 'bandstand' ),
			'filter_items_list'     => esc_html__( 'Filter tracks list', 'bandstand' ),
			'items_list_navigation' => esc_html__( 'Tracks list navigation', 'bandstand' ),
			'items_list'            => esc_html__( 'Tracks list', 'bandstand' ),
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
			1  => esc_html__( 'Track updated.', 'bandstand' ),
			2  => esc_html__( 'Custom field updated.', 'bandstand' ),
			3  => esc_html__( 'Custom field deleted.', 'bandstand' ),
			4  => esc_html__( 'Track updated.', 'bandstand' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( esc_html__( 'Track restored to revision from %s.', 'bandstand' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => esc_html__( 'Track published.', 'bandstand' ),
			7  => esc_html__( 'Track saved.', 'bandstand' ),
			8  => esc_html__( 'Track submitted.', 'bandstand' ),
			9  => sprintf(
				esc_html__( 'Track scheduled for: %s.', 'bandstand' ),
				/* translators: Publish box date format, see http://php.net/date */
				'<strong>' . date_i18n( esc_html__( 'M j, Y @ H:i', 'bandstand' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => esc_html__( 'Track draft updated.', 'bandstand' ),
			'preview' => esc_html__( 'Preview track', 'bandstand' ),
			'view'    => esc_html__( 'View track', 'bandstand' ),
		);
	}

	/**
	 * Sanitize track duration value.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value Duration string.
	 * @return string
	 */
	public function sanitize_duration( $value ) {
		return preg_replace( '/[^0-9:]/', '', $value );
	}
}
