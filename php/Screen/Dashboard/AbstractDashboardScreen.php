<?php
/**
 * Common dashboard screen functionality.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

namespace Bandstand\Screen\Dashboard;

use Bandstand\Screen\AbstractScreen;

/**
 * Class to extend for common functionality on dashboard screens.
 *
 * @package Bandstand\Administration
 * @since   1.0.0
 */
abstract class AbstractDashboardScreen extends AbstractScreen {
	/**
	 * Display the screen header.
	 *
	 * @since 1.0.0
	 */
	public function display_screen_header() {
		include( $this->plugin->get_path( 'admin/views/screen/dashboard-header.php' ) );
	}

	/**
	 * Display the screen footer.
	 *
	 * @since 1.0.0
	 */
	public function display_screen_footer() {
		include( $this->plugin->get_path( 'admin/views/screen/dashboard-footer.php' ) );
	}
}
