<?php
/**
 * Plugin initialization.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load the autoloader.
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
	require( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );
}

/**
 * Retrieve the Bandstand plugin instance.
 *
 * @since 1.0.0
 *
 * @return Bandstand_Plugin
 */
function bandstand() {
	static $instance;

	if ( null === $instance ) {
		$instance = new Bandstand_Plugin();
	}

	return $instance;
}

$bandstand = bandstand()
	->set_basename( plugin_basename( __FILE__ ) )
	->set_directory( plugin_dir_path( __FILE__ ) )
	->set_file( __DIR__ . '/bandstand.php' )
	->set_slug( 'bandstand' )
	->set_url( plugin_dir_url( __FILE__ ) );

/**
 * Load functions and libraries.
 */
require( $bandstand->get_path( 'includes/functions.php' ) );
require( $bandstand->get_path( 'includes/general-template.php' ) );

/**
 * Load admin functionality.
 */
if ( is_admin() ) {
	require( $bandstand->get_path( 'admin/functions.php' ) );
}

$bandstand
	->register_hooks( new Bandstand_Provider_Setup() )
	->register_hooks( new Bandstand_Provider_Widgets() )
	->register_hooks( new Bandstand_Provider_Assets() )
	->register_hooks( new Bandstand_Provider_GeneralHooks() )
	->register_hooks( new Bandstand_Provider_MediaHooks() )
	->register_hooks( new Bandstand_Provider_TemplateHooks() )
	->modules
	->register( new Bandstand_Module_Archives( $bandstand ) )
	->register( new Bandstand_Module_Gigs( $bandstand ) )
	->register( new Bandstand_Module_Discography( $bandstand ) )
	->register( new Bandstand_Module_Videos( $bandstand ) );

if ( is_admin() ) {
	$bandstand
		->register_hooks( new Bandstand_UpgradeManager() )
		->register_hooks( new Bandstand_Provider_AdminHooks() )
		->register_hooks( new Bandstand_AJAX_Admin() )
		->register_hooks( new Bandstand_Provider_AdminAssets() )
		->register_hooks( new Bandstand_Screen_Dashboard() )
		->register_hooks( new Bandstand_Screen_Settings() )
		->register_hooks( new Bandstand_Provider_Setting_GoogleMaps() );
}

if ( is_network_admin() ) {
	$bandstand->register_hooks( new Bandstand_Screen_Network_Settings() );
}

/**
 * Load the plugin.
 *
 * @since 1.0.0
 */
function bandstand_load() {
	bandstand()->load();
}
add_action( 'plugins_loaded', 'bandstand_load' );
