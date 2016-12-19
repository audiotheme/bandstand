<?php
/**
 * Widgets provider.
 *
 * @package   Bandstand\Widgets
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
 * Widgets provider class.
 *
 * @package Bandstand\Widgets
 * @since   1.0.0
 */
class Widgets implements HookProviderInterface, PluginAwareInterface {

	use PluginAwareTrait;

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register supported widgets.
	 *
	 * @since 1.0.0
	 */
	public function register_widgets() {
		$widgets = array();

		if ( $this->plugin->modules['discography']->is_active() ) {
			$widgets['record'] = '\\Bandstand\\Widget\\RecordWidget';
		}

		if ( $this->plugin->modules['gigs']->is_active() ) {
			$widgets['gigs'] = '\\Bandstand\\Widget\\GigsWidget';
		}

		if ( $this->plugin->modules['videos']->is_active() ) {
			$widgets['video']  = '\\Bandstand\\Widget\\VideoWidget';
		}

		if ( empty( $widgets ) ) {
			return;
		}

		foreach ( $widgets as $widget_class ) {
			register_widget( $widget_class );
		}
	}
}
