<?php
/**
 * Main plugin functionality.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

namespace Bandstand;

use Bandstand\Factory\PostFactory;
use Bandstand\Template\TemplateCompatibility;
use Bandstand\Template\TemplateLoader;
use Bandstand\Template\TemplateManager;

/**
 * Main plugin class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Plugin extends AbstractPlugin {
	/**
	 * Modules.
	 *
	 * @since 1.0.0
	 * @var ModuleCollection
	 */
	protected $modules;

	/**
	 * Post factory.
	 *
	 * @since 1.0.0
	 * @var PostFactory
	 */
	protected $post_factory;

	/**
	 * Template manager.
	 *
	 * @since 1.0.0
	 * @var TemplateManager
	 */
	protected $templates;

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->modules = new ModuleCollection();

		$this->post_factory = new PostFactory();

		$this->templates = new TemplateManager(
			$this,
			new TemplateLoader( $this ),
			new TemplateCompatibility()
		);
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
			case 'modules' :
				return $this->modules;
			case 'post_factory' :
				return $this->post_factory;
			case 'templates' :
				return $this->templates;
		}
	}

	/**
	 * Load the plugin.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		$this->load_modules();
	}

	/**
	 * Load the active modules.
	 *
	 * Modules are always loaded when viewing the Bandstand Settings screen so
	 * they can be toggled with instant access.
	 *
	 * @since 1.0.0
	 */
	protected function load_modules() {
		foreach ( $this->modules as $module ) {
			// Load all modules on the Dashboard screen.
			if ( ! $this->is_dashboard_screen() && ! $module->is_active() ) {
				continue;
			}

			$this->register_hooks( $module->load() );
		}
	}

	/**
	 * Whether the current request is the dashboard screen.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_dashboard_screen() {
		return is_admin() && isset( $_GET['page'] ) && 'bandstand' === $_GET['page'];
	}
}
