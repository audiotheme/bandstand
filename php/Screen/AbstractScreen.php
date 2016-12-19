<?php
/**
 * Base admin screen functionality.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

namespace Bandstand\Screen;

use Bandstand\HookProviderInterface;
use Bandstand\PluginAwareInterface;
use Bandstand\PluginAwareTrait;

/**
 * Base screen class.
 *
 * @package Bandstand\Administration
 * @since   1.0.0
 */
abstract class AbstractScreen implements HookProviderInterface, PluginAwareInterface {

	use PluginAwareTrait;

	/**
	 * Registers hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	abstract function register_hooks();

}
