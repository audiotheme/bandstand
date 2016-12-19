<?php
/**
 * Plugin aware interface.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

namespace Bandstand;

/**
 * Plugin aware interface.
 *
 * @package Bandstand
 * @since 1.0.0
 */
interface PluginAwareInterface {
	/**
	 * Set the main plugin instance.
	 *
	 * @param  PluginInterface $plugin Main plugin instance.
	 * @return $this
	 */
	public function set_plugin( PluginInterface $plugin );
}
