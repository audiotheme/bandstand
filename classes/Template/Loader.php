<?php
/**
 * Template loader.
 *
 * @package   Bandstand\Template
 * @copyright 2013 Gary Jones, 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Template loader class.
 *
 * @package Bandstand\Template
 * @since   1.0.0
 * @link    https://github.com/GaryJones/Gamajo-Template-Loader
 */
class Bandstand_Template_Loader {
	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Plugin
	 */
	protected $plugin;

	/**
	 * Prefix for filter names.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $filter_prefix = 'bandstand';

	/**
	 * Directory name where templates are found in this plugin.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $plugin_template_directory = 'templates';

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @param Bandstand_Plugin $plugin Main plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Clean up template data.
	 *
	 * @since 1.0.0
	 */
	public function __destruct() {
		$this->unset_template_data();
	}

	/**
	 * Retrieve a template part.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $slug The slug name for the generic template.
	 * @param  string $name Optional. The name of the specialised template.
	 */
	public function get_template_part( $slug, $name = null ) {
		do_action( 'get_template_part_' . $slug, $slug, $name );

		// Get files names of templates, for given slug and name.
		$templates = $this->get_template_file_names( $slug, $name );

		$this->locate_template( $templates, true, false );
	}

	/**
	 * Make custom data available to template.
	 *
	 * Data is available to the template as properties under the `$data` variable.
	 * i.e. A value provided here under `$data['foo']` is available as `$data->foo`.
	 *
	 * When an input key has a hyphen, you can use `$data->{foo-bar}` in the template.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data     Custom data for the template.
	 * @param string $var_name Optional. Variable under which the custom data is available in the template.
	 *                         Default is 'data'.
	 */
	public function set_template_data( array $data, $var_name = 'data' ) {
		global $wp_query;

		$wp_query->query_vars[ $var_name ] = (object) $data;
	}

	/**
	 * Remove access to custom data in template.
	 *
	 * Good to use once the final template part has been requested.
	 *
	 * @since 1.0.0
	 */
	public function unset_template_data() {
		global $wp_query;

		if ( isset( $wp_query->query_vars['data'] ) ) {
			unset( $wp_query->query_vars['data'] );
		}
	}

	/**
	 * Given a slug and optional name, create the file names of templates.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $slug Partial slug.
	 * @param  string $name Partial name.
	 * @return array
	 */
	protected function get_template_file_names( $slug, $name ) {
		$templates = array();
		if ( isset( $name ) ) {
			$templates[] = $slug . '-' . $name . '.php';
		}
		$templates[] = $slug . '.php';

		/**
		 * Allow template choices to be filtered.
		 *
		 * The resulting array should be in the order of most specific first, to least specific last.
		 * e.g. 0 => recipe-instructions.php, 1 => recipe.php
		 *
		 * @since 1.0.0
		 *
		 * @param array $templates Names of template files that should be looked for, for given slug and name.
		 * @param string $slug Template slug.
		 * @param string $name Template name.
		 */
		return apply_filters( $this->filter_prefix . '_get_template_part', $templates, $slug, $name );
	}

	/**
	 * Retrieve the name of the highest priority template file that exists.
	 *
	 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
	 * inherit from a parent theme can just overload one file. If the template
	 * is not found in either of those, it looks in the theme-compat folder last.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|array $template_names Template file(s) to search for, in order.
	 * @param  bool         $load           If true the template file will be loaded if it is found.
	 * @param  bool         $require_once   Whether to require_once or require. Default true. Has no effect if $load is false.
	 * @return string       The template filename if one is located.
	 */
	public function locate_template( $template_names, $load = false, $require_once = true ) {
		$located = false;

		// Remove empty entries.
		$template_names = array_filter( (array) $template_names );
		$template_paths = $this->get_template_paths();

		// Try to find a template file.
		foreach ( $template_names as $template_name ) {
			// Trim off any slashes from the template name.
			$template_name = ltrim( $template_name, '/' );

			// Try locating this template file by looping through the template paths.
			foreach ( $template_paths as $template_path ) {
				if ( file_exists( $template_path . $template_name ) ) {
					$located = $template_path . $template_name;
					break 2;
				}
			}
		}

		if ( $load && $located ) {
			load_template( $located, $require_once );
		}

		return $located;
	}

	/**
	 * Load a template file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_file Absolute path to a file or list of template parts.
	 * @param array  $data          Optional. List of variables to extract into the template scope.
	 */
	public function load_template( $template_file, $data = array() ) {
		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

		if ( is_array( $wp_query->query_vars ) ) {
			extract( $wp_query->query_vars, EXTR_SKIP );
		}

		if ( is_array( $data ) && ! empty( $data ) ) {
			extract( $data, EXTR_OVERWRITE );
			unset( $data );
		}

		if ( file_exists( $template_file ) ) {
			require( $template_file );
		}
	}

	/**
	 * Return a list of paths to check for template locations.
	 *
	 * Default is to check in a child theme (if relevant) before a parent theme,
	 * so that themes which inherit from a parent theme can just overload one
	 * file. If the template is not found in either of those, it looks in the
	 * theme-compat folder last.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_template_paths() {
		$theme_directory = trailingslashit( $this->plugin->get_slug() );

		$file_paths = array(
			10  => trailingslashit( get_template_directory() ) . $theme_directory,
			100 => $this->get_templates_directory(),
		);

		// Only add this conditionally, so non-child themes don't redundantly check active theme twice.
		// The is_child_theme() function compares constants and won't work in unit tests.
		if ( get_template() !== get_stylesheet() ) {
			$file_paths[1] = trailingslashit( get_stylesheet_directory() ) . $theme_directory;
		}

		/**
		 * Allow ordered list of template paths to be amended.
		 *
		 * @since 1.0.0
		 *
		 * @param array $var Default is directory in child theme at index 1, parent theme at 10, and plugin at 100.
		 */
		$file_paths = apply_filters( $this->filter_prefix . '_template_paths', $file_paths );

		// Sort the file paths based on priority.
		ksort( $file_paths, SORT_NUMERIC );

		return array_map( 'trailingslashit', $file_paths );
	}

	/**
	 * Return the path to the templates directory in this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_templates_directory() {
		return $this->plugin->get_path( $this->plugin_template_directory );
	}
}
