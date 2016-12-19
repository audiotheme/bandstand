<?php
/**
 * General hooks.
 *
 * @package   Bandstand
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
 * General hooks class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class GeneralHooks implements HookProviderInterface, PluginAwareInterface {

	use PluginAwareTrait;

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_filter( 'kses_allowed_protocols', array( $this, 'register_urn_protocol' ) );
	}

	/**
	 * Register urn: as an allowed protocol.
	 *
	 * The GUID gets passed through `esc_url_raw`, so we need to allow urn.
	 *
	 * @since 1.0.0
	 *
	 * @link https://github.com/rmccue/realguids
	 *
	 * @param  array $protocols Allowed protocols.
	 * @return array
	 */
	public function register_urn_protocol( $protocols ) {
		$protocols[] = 'urn';
		return $protocols;
	}
}
