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

namespace Bandstand\Provider;

use Bandstand\HookProviderInterface;
use Bandstand\PluginAwareInterface;
use Bandstand\PluginAwareTrait;

/**
 * Upgrade manager class.
 *
 * @package Bandstand\Administration
 * @since   1.0.0
 */
class UpgradeManager implements HookProviderInterface, PluginAwareInterface {

	use PluginAwareTrait;

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
