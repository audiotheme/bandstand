<?php
/**
 * Theme Compatibility
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Theme compatibility class.
 *
 * Plugins aren't aware of HTML wrappers are the main content defined in a
 * theme's templates, making it difficult to render custom templates.
 *
 * Themes can add support by printing their content wrappers in the
 * 'bandstand_before_content' and 'bandstand_after_content' actions and declaring
 * support with add_theme_support( 'bandstand' ).
 *
 * If a theme hasn't declared support and the located template is in the
 * plugin's /templates directory, then a compatible template in the theme
 * (like page.php) is loaded instead and the main $wp_query object is
 * manipulated in order to facilitate rendering.
 *
 * @link https://bbpress.trac.wordpress.org/browser/trunk/src/includes/core/theme-compat.php
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Template_Compatibility {
	/**
	 * Whether theme compat mode is enabled.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $is_active = false;

	/**
	 * The temporary post title if a compat template is loaded.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $title = '';

	/**
	 * Slug for the template part that renders the main loop.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $template_part_slug = '';

	/**
	 * Name for the template part that renders the main loop.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $template_part_name = '';

	/**
	 * Deep copy of the main query.
	 *
	 * @since 1.0.0
	 * @var WP_Query
	 */
	protected $the_query;

	/**
	 * Copy of any removed filters.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $filters = array();

	/**
	 * Whether the original main loop has started.
	 *
	 * This flag is set to true when the original main loop starts so the
	 * 'loop_start' filter isn't applied to any other loops. Removing a filter
	 * from within itself can cause issues.
	 *
	 * @link https://core.trac.wordpress.org/ticket/21169
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_main_loop_started = false;

	/**
	 * Enable theme compatibility.
	 *
	 * @since 1.0.0
	 */
	public function enable() {
		$this->is_active = true;

		// Time to start drinking. Hijack the main loop.
		add_action( 'loop_start', array( $this, 'loop_start' ) );
	}

	/**
	 * Retrieve the main content for the request.
	 *
	 * Loads the template part declared in set_loop_template_part().
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_content() {
		if ( empty( $this->template_part_slug ) ) {
			return '';
		}

		ob_start();
		get_bandstand_template_part( $this->template_part_slug, $this->template_part_name );
		return ob_get_clean();
	}

	/**
	 * Whether theme compatibility mode is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->is_active;
	}

	/**
	 * Set the template part for rendering the main loop.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 */
	public function set_loop_template_part( $slug, $name = null ) {
		$this->template_part_slug = $slug;
		$this->template_part_name = $name;
	}

	/**
	 * Retrieve the title for the current template.
	 *
	 * @since 1.0.0
	 */
	protected function get_title() {
		if ( ! empty( $this->title ) ) {
			return $this->title;
		}

		$title = '';

		if ( is_archive() ) {
			$title = get_the_archive_title();
		}

		return $title;
	}

	/**
	 * Set the title for the temporary post when a compat template is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Page title.
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	/*
	 * Protected methods.
	 */

	/**
	 * Filter the main loop as it gets started.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query Main query object.
	 */
	public function loop_start( $wp_query ) {
		// Only run this during the main loop.
		if ( ! $wp_query->is_main_query() || $this->has_main_loop_started ) {
			return;
		}

		// Set a flag so this method won't run again.
		$this->has_main_loop_started = true;

		// Create a deep clone of $wp_query.
		$this->the_query = new WP_Query( $wp_query->query );

		// Make a temporary post for the default main loop.
		$post = $this->get_temporary_post(
			array(
				'post_content' => $this->get_content(),
				'post_title'   => $this->get_title(),
			),
			is_singular() ? $wp_query->post : array()
		);

		$post = new WP_Post( (object) $post );
		$this->replace_the_query( $post );
		$this->disable_filters();

		// Restore the query and filters when this loop ends.
		add_action( 'loop_end', array( $this, 'loop_end' ) );
	}

	/**
	 * Clean up after the main loop has finished.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query Main query object.
	 */
	public function loop_end( $wp_query ) {
		if ( ! $wp_query->is_main_query() ) {
			return;
		}

		$this->restore_the_query();
		$this->restore_filters();
	}

	/**
	 * Retrieve a temporary post object.
	 *
	 * @since 1.0.0
	 *
	 * @see get_default_post_to_edit()
	 * @see bbp_theme_compat_reset_post()
	 *
	 * @param array $args Optional. Properties to assign to the temporary post.
	 * @param array $defaults Optional. Properties that should override the default temporary properties.
	 * @return array
	 */
	protected function get_temporary_post( $args = array(), $defaults = array() ) {
		if ( ! empty( $defaults ) ) {
			$args = wp_parse_args( $args, (array) $defaults );
		}

		$post = wp_parse_args( $args, array(
			'ID'                    => -9999,
			'post_author'           => 0,
			'post_date'             => 0,
			'post_date_gmt'         => 0,
			'post_content'          => '',
			'post_title'            => '',
			'post_excerpt'          => '',
			'post_status'           => 'publish',
			'comment_status'        => 'closed',
			'ping_status'           => '',
			'post_password'         => '',
			'post_name'             => '',
			'to_ping'               => '',
			'pinged'                => '',
			'post_modified'         => 0,
			'post_modified_gmt'     => 0,
			'post_content_filtered' => '',
			'post_parent'           => 0,
			'guid'                  => '',
			'menu_order'            => 0,
			'post_type'             => 'page',
			'post_mime_type'        => '',
			'comment_count'         => 0,
			'page_template'         => 'default',
			'filter'                => 'raw',
		) );

		return $post;
	}

	/**
	 * Replace properties in $wp_query.
	 *
	 * @since 1.0.0
	 *
	 * @global WP_Query $wp_query
	 *
	 * @param WP_Post $post Post to replace in the query.
	 */
	protected function replace_the_query( $post ) {
		global $wp_query;

		$wp_query->post        = $post;
		$wp_query->posts       = array( $post );
		$wp_query->post_count  = 1;
		$wp_query->found_posts = 1;
		$wp_query->is_archive  = false;
		$wp_query->is_singular = true;

		// Attempt to disable the comment form if the current post doesn't support them.
		if ( 'open' !== $post->comment_status || ! post_type_supports( $post->post_type, 'comments' ) ) {
			$wp_query->is_single  = false;
		}

		do_action_ref_array( 'bandstand_replace_the_query', array( &$wp_query ) );
	}

	/**
	 * Restore the query.
	 *
	 * @since 1.0.0
	 *
	 * @global WP_Query $wp_query
	 */
	protected function restore_the_query() {
		global $wp_query;

		if ( ! isset( $this->the_query ) ) {
			return;
		}

		$wp_query->post        = $this->the_query->post;
		$wp_query->posts       = $this->the_query->posts;
		$wp_query->post_count  = $this->the_query->post_count;
		$wp_query->found_posts = $this->the_query->found_posts;
		$wp_query->is_404      = $this->the_query->is_404;
		$wp_query->is_archive  = $this->the_query->is_archive;
		$wp_query->is_page     = $this->the_query->is_page;
		$wp_query->is_single   = $this->the_query->is_single;
		$wp_query->is_singular = $this->the_query->is_singular;
		$wp_query->is_tax      = $this->the_query->is_tax;

		// Break ties with the lies.
		unset( $this->the_query );
	}

	/**
	 * Disable standard filters that shouldn't be run on the temporary post in a
	 * compat template.
	 *
	 * @since 1.0.0
	 */
	protected function disable_filters() {
		// Disable post thumbnails.
		add_filter( 'get_post_metadata', array( $this, 'disable_post_thumbnails' ), 20, 3 );

		// Remove any filters from the_content.
		$this->remove_all_filters( 'the_content' );
	}

	/**
	 * Prevent post thumbnails from being displayed for the temporary post.
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed  $value Filtered post meta value.
	 * @param  int    $post_id Post ID.
	 * @param  string $key Post meta key.
	 * @return mixed Post meta value.
	 */
	public function disable_post_thumbnails( $value, $post_id, $key ) {
		if ( '_thumbnail_id' === $key ) {
			$value = '';
		}
		return $value;
	}

	/**
	 * Restore filters.
	 *
	 * @since 1.0.0
	 */
	protected function restore_filters() {
		remove_filter( 'get_post_metadata', array( $this, 'disable_post_thumbnails' ), 20, 3 );

		// Restore the_content filters.
		$this->restore_all_filters( 'the_content' );
	}

	/**
	 * Remove all the hooks from a filter and save a reference.
	 *
	 * @since 1.0.0
	 *
	 * @see bbp_remove_all_filters()
	 * @global array $wp_filter
	 *
	 * @param  string $tag The filter to remove hooks from.
	 * @return bool True when finished.
	 */
	protected function remove_all_filters( $tag ) {
		global $wp_filter;

		if ( ! isset( $wp_filter[ $tag ] ) ) {
			return true;
		}

		$this->filters[ $tag ] = $wp_filter[ $tag ];

		if ( $wp_filter[ $tag ] instanceof WP_Hook ) {
			$wp_filter[ $tag ]->callbacks = array();
		} else {
			$wp_filter[ $tag ] = array();
		}

		return true;
	}

	/**
	 * Restore all hooks that were removed from a filter.
	 *
	 * @since 1.0.0
	 *
	 * @see bbp_restore_all_filters()
	 * @global array $wp_filter
	 *
	 * @param  string $tag The filter to remove hooks from.
	 * @return bool True when finished.
	 */
	protected function restore_all_filters( $tag ) {
		global $wp_filter;

		if ( ! isset( $wp_filter[ $tag ] ) ) {
			return true;
		}

		if ( $wp_filter[ $tag ] instanceof WP_Hook ) {
			$wp_filter[ $tag ]->callbacks = $this->filters[ $tag ];
		} else {
			$wp_filter[ $tag ] = $this->filters[ $tag ];
		}

		unset( $this->filters[ $tag ] );

		return true;
	}
}
