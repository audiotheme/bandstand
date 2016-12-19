<?php
/**
 * Upgrade manager.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Upgrade manager class.
 *
 * @package Bandstand\Administration
 * @since   1.0.0
 */
class Bandstand_UpgradeManager {
	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Plugin
	 */
	protected $plugin;

	/**
	 * Set a reference to a plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @param Bandstand_Plugin $plugin Main plugin instance.
	 * @return $this
	 */
	public function set_plugin( Bandstand_Plugin $plugin ) {
		$this->plugin = $plugin;
		return $this;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'admin_init', array( $this, 'maybe_upgrade' ) );
	}

	/**
	 * Upgrade routine.
	 *
	 * @since 1.0.0
	 */
	public function maybe_upgrade() {
		$saved_version   = get_option( 'bandstand_version', '0' );
		$current_version = BANDSTAND_VERSION;

		if ( '0' === $saved_version || version_compare( $saved_version, $current_version, '<' ) ) {
			update_option( 'bandstand_version', BANDSTAND_VERSION );
		}
	}
}
