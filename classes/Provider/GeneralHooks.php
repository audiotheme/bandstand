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

/**
 * General hooks class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Provider_GeneralHooks extends Bandstand_AbstractProvider {
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
