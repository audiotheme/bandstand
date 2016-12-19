<?php
/**
 * Network settings screen functionality.
 *
 * @package   Bandstand\Settings
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Network settings screen class.
 *
 * @package Bandstand\Settings
 * @since   1.0.0
 */
class Bandstand_Screen_Network_Settings extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'network_admin_menu', array( $this, 'add_menu_item' ) );
		add_action( 'admin_init',         array( $this, 'add_sections' ) );
		add_action( 'admin_init',         array( $this, 'save_network_settings' ) );
	}

	/**
	 * Add the settings menu item.
	 *
	 * @since 1.0.0
	 */
	public function add_menu_item() {
		add_submenu_page(
			'settings.php',
			esc_html__( 'Settings', 'bandstand' ),
			esc_html__( 'Bandstand', 'bandstand' ),
			'manage_network_options',
			'bandstand-network-settings',
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
			'bandstand-network-settings'
		);
	}

	/**
	 * Display the screen.
	 *
	 * @since 1.0.0
	 */
	public function display_screen() {
		include( $this->plugin->get_path( 'admin/views/screen-network-settings.php' ) );
	}

	/**
	 * Fire an action when a network settings screen is saved.
	 *
	 * Plugins need to manually save each registered options. Check the nonce in
	 * $_POST['_wpnonce'] to be sure the action is '{$option_group}-options'.
	 *
	 * Don't call wp_die() or exit() since all network settings screens will use
	 * the same action.
	 *
	 * @since 1.0.0
	 */
	public function save_network_settings() {
		if ( ! is_network_admin() || empty( $_GET['action'] ) || 'bandstand-save-network-settings' !== $_GET['action'] ) {
			return;
		}

		do_action( 'bandstand_save_network_settings' );

		$redirect = add_query_arg( 'page', 'bandstand-network-settings', admin_url( 'network/settings.php' ) );
		wp_safe_redirect( esc_url_raw( $redirect ) );
		exit;
	}
}
