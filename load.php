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

use Bandstand\AJAX;
use Bandstand\Plugin;
use Bandstand\Module;
use Bandstand\Provider;
use Bandstand\Screen;

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
 * @return \Bandstand\Plugin
 */
function bandstand() {
	static $instance;

	if ( null === $instance ) {
		$instance = new Plugin();
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
	->register_hooks( new Provider\Setup() )
	->register_hooks( new Provider\Widgets() )
	->register_hooks( new Provider\Assets() )
	->register_hooks( new Provider\GeneralHooks() )
	->register_hooks( new Provider\MediaHooks() )
	->register_hooks( new Provider\TemplateHooks() )
	->modules
	->register( new Module\ArchivesModule( $bandstand ) )
	->register( new Module\GigsModule( $bandstand ) )
	->register( new Module\DiscographyModule( $bandstand ) )
	->register( new Module\VideosModule( $bandstand ) );

if ( is_admin() ) {
	$bandstand
		->register_hooks( new Provider\UpgradeManager() )
		->register_hooks( new Provider\AdminHooks() )
		->register_hooks( new AJAX\Admin() )
		->register_hooks( new Provider\AdminAssets() )
		->register_hooks( new Screen\Dashboard() )
		->register_hooks( new Screen\Settings() )
		->register_hooks( new Provider\Setting\GoogleMaps() );
}

if ( is_network_admin() ) {
	$bandstand->register_hooks( new Screen\Network\Settings() );
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
