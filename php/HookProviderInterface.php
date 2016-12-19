<?php
/**
 * Hook provider interface.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

namespace Bandstand;

/**
 * Hook provider interface.
 *
 * @package Bandstand
 * @since 1.0.0
 */
interface HookProviderInterface {
	/**
	 * Register hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks();
}
