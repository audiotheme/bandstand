<?php
/**
 * Dashboard screen functionality.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Dashboard screen class.
 *
 * @package Bandstand\Administration
 * @since   1.0.0
 */
class Bandstand_Screen_Dashboard extends Bandstand_Screen_AbstractScreen {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		add_action( 'wp_ajax_bandstand_ajax_toggle_module', array( $this, 'ajax_toggle_module' ) );
	}

	/**
	 * Add menu items.
	 *
	 * @since 1.0.0
	 */
	public function add_menu_item() {
		$page_hook = add_menu_page(
			esc_html__( 'Bandstand', 'bandstand' ),
			esc_html__( 'Bandstand', 'bandstand' ),
			'edit_posts',
			'bandstand',
			array( $this, 'display_screen' ),
			bandstand_encode_svg( 'admin/images/dashicons/bandstand.svg' ),
			511
		);

		add_submenu_page(
			'bandstand',
			esc_html__( 'Features', 'bandstand' ),
			esc_html__( 'Features', 'bandstand' ),
			'edit_posts',
			'bandstand',
			array( $this, 'display_screen' )
		);

		add_action( 'load-' . $page_hook, array( $this, 'load_screen' ) );
	}

	/**
	 * Set up the main Dashboard screen.
	 *
	 * @since 1.0.0
	 */
	public function load_screen() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue assets for the Dashboard screen.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		$modules = $this->plugin->modules;

		wp_enqueue_script( 'bandstand-dashboard' );
		wp_enqueue_style( 'bandstand-dashboard' );

		// Hide menu items for inactive modules on initial load.
		$styles = '';
		foreach ( $modules->get_inactive_keys() as $module_id ) {
			$styles .= sprintf(
				'#%1$s, .wp-submenu > li.%1$s { display: none;}',
				$modules[ $module_id ]->admin_menu_id
			);
		}

		wp_add_inline_style( 'bandstand-dashboard', $styles );
	}

	/**
	 * Display the screen header.
	 *
	 * @since 1.0.0
	 */
	public function display_screen_header() {
		include( $this->plugin->get_path( 'admin/views/screen-dashboard-header.php' ) );
	}

	/**
	 * Display the screen footer.
	 *
	 * @since 1.0.0
	 */
	public function display_screen_footer() {
		include( $this->plugin->get_path( 'admin/views/screen-dashboard-footer.php' ) );
	}

	/**
	 * Display the Dashboard screen.
	 *
	 * @since 1.0.0
	 */
	public function display_screen() {
		$modules = $this->plugin->modules;
		foreach ( $modules as $id => $module ) {
			if ( ! $module->show_in_dashboard() ) {
				unset( $modules[ $id ] );
			}
		}

		$this->display_screen_header();
		include( $this->plugin->get_path( 'admin/views/screen-dashboard-modules.php' ) );
		$this->display_screen_footer();
		include( $this->plugin->get_path( 'admin/views/templates-dashboard.php' ) );
	}

	/**
	 * Toggle a module's status.
	 *
	 * @since 1.0.0
	 */
	public function ajax_toggle_module() {
		if ( empty( $_POST['module'] ) ) {
			wp_send_json_error();
		}

		$module_id = sanitize_key( $_POST['module'] );

		check_ajax_referer( 'toggle-module_' . $module_id, 'nonce' );

		$modules = $this->plugin->modules;
		$module  = $modules[ $module_id ];

		if ( $module->is_active() ) {
			$modules->deactivate( $module_id );
		} else {
			$modules->activate( $module_id );
		}

		wp_send_json_success( $module->prepare_for_js() );
	}
}
