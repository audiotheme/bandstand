<?php
/**
 * Template hooks provider.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Template hooks provider class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Provider_TemplateHooks extends Bandstand_AbstractProvider {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'wp_head',                    array( $this, 'document_javascript_support' ) );
		add_filter( 'body_class',                 array( $this, 'body_classes' ) );
		add_filter( 'wp_nav_menu_objects',        array( $this, 'nav_menu_classes' ), 10, 3 );
		add_filter( 'bandstand_archive_title',    array( $this, 'taxonomy_archives_titles' ) );
		add_action( 'bandstand_before_loop',      array( $this, 'display_archive_description' ) );
		add_filter( 'navigation_markup_template', array( $this, 'navigation_markup_template' ) );
	}

	/**
	 * Add a 'js' class to the html element if JavaScript is enabled.
	 *
	 * @since 1.0.0
	 */
	public function document_javascript_support() {
		?>
		<script>
		var classes = document.documentElement.className.replace( 'no-js', 'js' );
		document.documentElement.className += /^js$|^js | js$| js /.test( classes ) ? '' : ' js';
		</script>
		<?php
	}

	/**
	 * Add HTML classes to the body element.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Array of classes.
	 * @return array
	 */
	public function body_classes( $classes ) {
		if ( bandstand()->templates->compatibility->is_active() ) {
			$classes[] = 'bandstand-theme-compat';
		}

		return $classes;
	}

	/**
	 * Add helpful nav menu item classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items List of menu items.
	 * @param array $args Menu display args.
	 * @return array
	 */
	public function nav_menu_classes( $items, $args ) {
		global $wp;

		if ( is_404() || is_search() ) {
			return $items;
		}

		$current_url  = trailingslashit( home_url( add_query_arg( array(), $wp->request ) ) );
		$blog_page_id = get_option( 'page_for_posts' );
		$is_blog_post = is_singular( 'post' );

		$is_bandstand_post_type = is_singular( array( 'bandstand_gig', 'bandstand_record', 'bandstand_track', 'bandstand_video' ) );
		$post_type_archive_id   = get_bandstand_post_type_archive( get_post_type() );
		$post_type_archive_link = get_post_type_archive_link( get_post_type() );

		$current_menu_parents = array();

		foreach ( $items as $key => $item ) {
			if (
				'bandstand_archive' === $item->object &&
				$post_type_archive_id === $item->object_id &&
				trailingslashit( $item->url ) === $current_url
			) {
				$items[ $key ]->classes[] = 'current-menu-item';
				$current_menu_parents[] = $item->menu_item_parent;
			}

			if ( $is_blog_post && $blog_page_id === $item->object_id ) {
				$items[ $key ]->classes[] = 'current-menu-parent';
				$current_menu_parents[] = $item->menu_item_parent;
			}

			// Add 'current-menu-parent' class to CPT archive links when viewing a singular template.
			if ( $is_bandstand_post_type && $post_type_archive_link === $item->url ) {
				$items[ $key ]->classes[] = 'current-menu-parent';
			}
		}

		// Add 'current-menu-parent' classes.
		$current_menu_parents = array_filter( $current_menu_parents );

		if ( ! empty( $current_menu_parents ) ) {
			foreach ( $items as $key => $item ) {
				if ( in_array( $item->ID, $current_menu_parents, true ) ) {
					$items[ $key ]->classes[] = 'current-menu-parent';
				}
			}
		}

		return $items;
	}

	/**
	 * Filter record type archive titles.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Archive title.
	 * @return string
	 */
	public function taxonomy_archives_titles( $title ) {
		if ( is_tax() ) {
			$title = get_queried_object()->name;
		}

		return $title;
	}

	/**
	 * Display the archive description in compatibility mode.
	 *
	 * @since 1.0.0
	 */
	public function display_archive_description() {
		if ( ! $this->is_bandstand_archive() || ! $this->plugin->templates->compatibility->is_active() ) {
			return;
		}

		the_bandstand_archive_description( '<div class="bandstand-archive-intro archive-intro">', '</div>' );
	}

	/**
	 * Filter the posts navigation template to add extra classes for styling.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $template Navigation HTML markup.
	 * @return string
	 */
	public function navigation_markup_template( $template ) {
		if ( ! $this->is_bandstand_archive() ) {
			return $template;
		}

		$class = '';

		if (
			is_post_type_archive( array( 'bandstand_record', 'bandstand_video' ) ) ||
			is_tax( array( 'bandstand_genre', 'bandstand_record_type', 'bandstand_video_category' ) )
		) {
			$class = 'reverse';
		}

		return preg_replace(
			'/class="([^"]+)"/',
			'class="$1 bandstand-posts-navigation ' . $class . '"',
			$template,
			1
		);
	}

	/**
	 * Whether the current archive is for a Bandstand post type or taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	protected function is_bandstand_archive() {
		$post_types = array( 'bandstand_gig', 'bandstand_record', 'bandstand_video' );
		$taxonomies = array( 'bandstand_genre', 'bandstand_record_type', 'bandstand_video_category' );
		return is_post_type_archive( $post_types ) || is_tax( $taxonomies );
	}
}
