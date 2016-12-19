<?php
/**
 * Template manager.
 *
 * @todo Allow for loading templates from the following sources:
 * - child theme
 * - parent theme
 * - template pack plugin
 * - plugin/templates/theme
 * - plugin/templates/default
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Template manager class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Template_Manager {
	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Plugin
	 */
	protected $plugin;

	/**
	 * Template loader.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Template_Loader
	 */
	protected $loader;

	/**
	 * Template compatibility.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Template_Compatibility
	 */
	protected $compatibility;

	/**
	 * Constructor method.
	 *
	 * @param Bandstand_Plugin                 $plugin        Main plugin instance.
	 * @param Bandstand_Template_Loader        $loader        Template loader.
	 * @param Bandstand_Template_Compatibility $compatibility Template compatibility.
	 */
	public function __construct( $plugin, $loader, $compatibility ) {
		$this->plugin = $plugin;
		$this->loader = $loader;
		$this->compatibility = $compatibility;
	}

	/**
	 * Magic get method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'loader' :
				return $this->loader;
			case 'compatibility' :
				return $this->compatibility;
		}
	}

	/**
	 * Magic call method.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name      Method name.
	 * @param  array  $arguments Method arguments.
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		switch ( $name ) {
			case 'set_title' :
			case 'set_loop_template_part' :
				return call_user_func_array( array( $this->compatibility, $name ), $arguments );
		}
	}

	/**
	 * Retrieve the name of the highest priority template that exists.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $templates Array of template files.
	 * @return string
	 */
	public function locate_template( $templates ) {
		$template = $this->loader->locate_template( $templates );
		return $this->get_compatible_template( $template );
	}

	/**
	 * Whether a template file is compatible with the theme.
	 *
	 * The template is either being loaded from the theme or the theme has
	 * declared support and has hooks attached to
	 * 'bandstand_before_content' and 'bandstand_after_content' to output the
	 * appropriate wrappers.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template file path.
	 * @return bool
	 */
	protected function is_template_compatible( $template ) {
		return current_theme_supports( 'bandstand' ) || ! $this->is_plugin_template( $template );
	}

	/**
	 * Retrieve a template that's compatible with the theme.
	 *
	 * Ensures the given template is compatible with theme, otherwise theme
	 * compatibility mode is enabled and a generic template is located from the
	 * theme to use in instead.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $template Template filename.
	 * @return string
	 */
	protected function get_compatible_template( $template ) {
		// Enable theme compatibility.
		if ( ! $this->is_template_compatible( $template ) ) {
			$this->compatibility->enable();
			$template = $this->get_theme_template();
		}

		return $template;
	}

	/**
	 * Retrieve a template from the theme.
	 *
	 * @since 1.0.0
	 *
	 * @link https://core.trac.wordpress.org/ticket/20509
	 * @link https://core.trac.wordpress.org/ticket/22355
	 *
	 * @return string The template path if one is located.
	 */
	protected function get_theme_template() {
		// If the template is being loaded from the plugin and the theme hasn't
		// declared support, search for a compatible template in the theme.
		$template = locate_template( array(
			'plugin-bandstand.php',
			'bandstand.php',
			'generic.php',
			'page.php',
			'singular.php',
			'index.php',
		) );

		return $template;
	}

	/**
	 * Whether a template file is located in the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template file path.
	 * @return bool
	 */
	protected function is_plugin_template( $template ) {
		$template = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, $template );
		return ( 0 === strpos( $template, $this->plugin->get_path() ) );
	}
}
