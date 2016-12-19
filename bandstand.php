<?php
/**
 * Main plugin file.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: Bandstand
 * Plugin URI:  https://wordpress.org/plugins/bandstand/
 * Description: A platform for music-oriented websites, allowing for easy management of gigs, discography, videos and more.
 * Version:     0.1.0
 * Author:      AudioTheme
 * Author URI:  https://audiotheme.com/
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bandstand
 * Requires at least: 4.7.0
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc., 59
 * Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The plugin version.
 */
define( 'BANDSTAND_VERSION', '0.1.0' );

/**
 * Load the compatibility checker.
 */
require_once( dirname( __FILE__ ) . '/classes/Compatibility.php' );

/**
 * Load the plugin or display a notice about requirements.
 */
if ( version_compare( phpversion(), Bandstand_Compatibility::MINIMUM_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', array( 'Bandstand_Compatibility', 'display_php_version_notice' ) );
} else {
	require( dirname( __FILE__ ) . '/plugin.php' );
}
