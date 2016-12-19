<?php
/**
 * Administration assets provider.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Administration assets provider class.
 *
 * @package Bandstand\Administration
 * @since   1.0.0
 */
class Bandstand_Provider_AdminAssets extends Bandstand_AbstractProvider {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register admin scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function register_assets() {
		$base_url = set_url_scheme( $this->plugin->get_url( 'admin/js' ) );
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'bandstand-admin',     $base_url . '/admin.bundle' . $suffix . '.js', array( 'jquery-ui-sortable', 'underscore', 'wp-util' ), BANDSTAND_VERSION, true );
		wp_register_script( 'bandstand-dashboard', $base_url . '/dashboard.js',                   array( 'jquery', 'wp-backbone', 'wp-util' ),            BANDSTAND_VERSION, true );
		wp_register_script( 'bandstand-media',     $base_url . '/media' . $suffix . '.js',        array( 'jquery' ),                                      BANDSTAND_VERSION, true );
		wp_register_script( 'bandstand-settings',  $base_url . '/settings' . $suffix . '.js',     array(),                                                BANDSTAND_VERSION, true );

		wp_localize_script( 'bandstand-admin', '_bandstandAdminSettings', array(

		) );

		wp_localize_script( 'bandstand-dashboard', '_bandstandDashboardSettings', array(
			'canActivateModules' => current_user_can( 'activate_plugins' ),
			'modules'            => bandstand()->modules->prepare_for_js(),
			'l10n'               => array(
				'activate'   => __( 'Activate', 'bandstand' ),
				'deactivate' => __( 'Deactivate', 'bandstand' ),
			),
		) );

		wp_localize_script( 'bandstand-media', '_bandstandMediaControl', array(
			'audioFiles'      => __( 'Audio files', 'bandstand' ),
			'frameTitle'      => __( 'Choose an Attachment', 'bandstand' ),
			'frameUpdateText' => __( 'Update Attachment', 'bandstand' ),
		) );

		$base_url = set_url_scheme( $this->plugin->get_url( 'admin/css' ) );

		wp_register_style( 'bandstand-admin',     $base_url . '/admin.min.css' );
		wp_register_style( 'bandstand-dashboard', $base_url . '/dashboard.min.css' );
	}

	/**
	 * Enqueue global admin scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_script( 'bandstand-admin' );
		wp_enqueue_style( 'bandstand-admin' );
	}
}
