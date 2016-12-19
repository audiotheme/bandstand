<?php
/**
 * Settings screen functionality.
 *
 * @package   Bandstand\Settings
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Settings screen class.
 *
 * @package Bandstand\Settings
 * @since   1.0.0
 */
class Bandstand_Screen_Settings extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		add_action( 'admin_init', array( $this, 'add_sections' ) );
	}

	/**
	 * Add the settings menu item.
	 *
	 * @since 1.0.0
	 */
	public function add_menu_item() {
		add_submenu_page(
			'bandstand',
			esc_html__( 'Settings', 'bandstand' ),
			esc_html__( 'Settings', 'bandstand' ),
			'manage_options',
			'bandstand-settings',
			array( $this, 'display_screen' )
		);
	}

	/**
	 * Add settings sections.
	 *
	 * @since 1.0.0
	 */
	public function add_sections() {
		add_settings_section(
			'default',
			'',
			'__return_null',
			'bandstand-settings'
		);
	}

	/**
	 * Display the screen.
	 *
	 * @since 1.0.0
	 */
	public function display_screen() {
		include( $this->plugin->get_path( 'admin/views/screen-settings.php' ) );
	}
}
