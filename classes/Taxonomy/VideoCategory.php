<?php
/**
 * Video category taxonomy registration and integration.
 *
 * @package   Bandstand\Videos
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class for registering the video category taxonomy and integration.
 *
 * @package Bandstand\Videos
 * @since   1.0.0
 */
class Bandstand_Taxonomy_VideoCategory extends Bandstand_AbstractProvider {
	/**
	 * Module.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Module_Videos
	 */
	protected $module;

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $taxonomy = 'bandstand_video_category';

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @param Bandstand_Module_Videos $module Videos module.
	 */
	public function __construct( $module ) {
		$this->module = $module;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'init',                  array( $this, 'register_taxonomy' ) );
		add_action( 'pre_get_posts',         array( $this, 'video_category_query' ), 9 );
		add_action( 'term_updated_messages', array( $this, 'term_updated_messages' ) );
	}

	/**
	 * Register taxonomies.
	 *
	 * @since 1.0.0
	 */
	public function register_taxonomy() {
		register_taxonomy( 'bandstand_video_category', 'bandstand_video', $this->get_args() );
	}

	/**
	 * Set video category requests to use the same archive settings as videos.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query The main WP_Query instance. Passed by reference.
	 */
	public function video_category_query( $wp_query ) {
		if ( is_admin() || ! $wp_query->is_main_query() || ! is_tax( $this->taxonomy ) ) {
			return;
		}

		$this->plugin->modules['archives']->set_current_archive_post_type( 'bandstand_video' );
	}

	/**
	 * Term updated messages.
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages Term update messages.
	 * @return array
	 */
	public function term_updated_messages( $messages ) {
		$messages[ $this->taxonomy ] = array(
			0 => '', // 0 = unused. Messages start at index 1.
			1 => esc_html__( 'Category added.', 'bandstand' ),
			2 => esc_html__( 'Category deleted.', 'bandstand' ),
			3 => esc_html__( 'Category updated.', 'bandstand' ),
			4 => esc_html__( 'Category not added.', 'bandstand' ),
			5 => esc_html__( 'Category not updated.', 'bandstand' ),
			6 => esc_html__( 'Categories deleted.', 'bandstand' ),
		);

		return $messages;
	}

	/**
	 * Retrieve taxonomy registration arguments.
	 *
	 * @since 1.0.0
	 */
	protected function get_args() {
		return array(
			'args'              => array( 'orderby' => 'term_order' ),
			'hierarchical'      => true,
			'labels'            => $this->get_labels(),
			'public'            => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'          => $this->module->get_rewrite_base() . '/' . $this->get_rewrite_base(),
				'with_front'    => false,
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
		);
	}

	/**
	 * Retrieve taxonomy labels.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_labels() {
		return array(
			'name'                       => esc_html_x( 'Categories', 'taxonomy general name', 'bandstand' ),
			'singular_name'              => esc_html_x( 'Category', 'taxonomy singular name', 'bandstand' ),
			'search_items'               => esc_html__( 'Search Categories', 'bandstand' ),
			'popular_items'              => esc_html__( 'Popular Categories', 'bandstand' ),
			'all_items'                  => esc_html__( 'All Categories', 'bandstand' ),
			'parent_item'                => esc_html__( 'Parent Category', 'bandstand' ),
			'parent_item_colon'          => esc_html__( 'Parent Category:', 'bandstand' ),
			'edit_item'                  => esc_html__( 'Edit Category', 'bandstand' ),
			'view_item'                  => esc_html__( 'View Category', 'bandstand' ),
			'update_item'                => esc_html__( 'Update Category', 'bandstand' ),
			'add_new_item'               => esc_html__( 'Add New Category', 'bandstand' ),
			'new_item_name'              => esc_html__( 'New Category Name', 'bandstand' ),
			'separate_items_with_commas' => esc_html__( 'Separate categories with commas', 'bandstand' ),
			'add_or_remove_items'        => esc_html__( 'Add or remove categories', 'bandstand' ),
			'choose_from_most_used'      => esc_html__( 'Choose from most used categories', 'bandstand' ),
			'menu_name'                  => esc_html_x( 'Categories', 'admin menu name', 'bandstand' ),
			'not_found'                  => esc_html__( 'No categories found.', 'bandstand' ),
			'no_terms'                   => esc_html__( 'No categories', 'bandstand' ),
			'items_list_navigation'      => esc_html__( 'Categories list navigation', 'bandstand' ),
			'items_list'                 => esc_html__( 'Categories list', 'bandstand' ),
		);
	}

	/**
	 * Retrieve the base slug to use for rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_rewrite_base() {
		$slug = preg_replace( '/[^a-z0-9-_]/', '', _x( 'category', 'video category permalink slug', 'bandstand' ) );

		if ( empty( $slug ) ) {
			$slug = 'category';
		}

		return apply_filters( 'bandstand_video_category_rewrite_base', $slug );
	}
}
