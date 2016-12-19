<?php
/**
 * Assets provider.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Assets provider class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Provider_Assets extends Bandstand_AbstractProvider {
	/**
	 * File suffix for minified assets.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $suffix = '.min';

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$this->suffix = '';
		}
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'wp_enqueue_scripts',    array( $this, 'register_assets' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ), 1 );

		// Enqueue after theme styles.
		add_action( 'wp_enqueue_scripts',    array( $this, 'enqueue_assets' ), 20 );
	}

	/**
	 * Register frontend scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function register_assets() {
		global $wp_locale;

		$base_url = $this->plugin->get_url( 'includes/js' );
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'bandstand',               $base_url . '/bandstand.bundle' . $suffix . '.js',  array( 'backbone', 'jquery', 'underscore' ), '1.0.0',  true );
		wp_register_script( 'bandstand-media-classes', $base_url . '/media-classes.js',                    array( 'jquery' ), '1.0.0',  true );
		wp_register_script( 'bandstand-tracklists',    $base_url . '/tracklists.bundle' . $suffix . '.js', array( 'backbone', 'jquery', 'underscore' ), '1.0.0',  true );
		wp_register_script( 'jquery-timepicker',       $base_url . '/vendor/jquery.timepicker.min.js',     array( 'jquery' ), '1.11.9', true );
		wp_register_script( 'moment',                  $base_url . '/vendor/moment.min.js',                array(),           '2.17.1', true );
		wp_register_script( 'pikaday',                 $base_url . '/vendor/pikaday.min.js',               array( 'moment'),  '1.5.1',  true );

		wp_localize_script( 'pikaday', '_pikadayL10n', array(
			'previousMonth' => __( 'Previous Month', 'bandstand' ),
			'nextMonth'     => __( 'Next Month', 'bandstand' ),
			'months'        => array_values( $wp_locale->month ),
			'weekdays'      => $wp_locale->weekday,
			'weekdaysShort' => array_values( $wp_locale->weekday_abbrev ),
		) );

		wp_register_style( 'bandstand', $this->plugin->get_url( 'includes/css/bandstand.min.css' ) );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		if ( ! apply_filters( 'bandstand_enqueue_theme_assets', true ) ) {
			return;
		}

		wp_enqueue_script( 'bandstand' );
		wp_enqueue_script( 'bandstand-media-classes' );
		wp_enqueue_style( 'bandstand' );
	}
}
