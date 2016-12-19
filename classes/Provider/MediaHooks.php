<?php
/**
 * Media hooks provider.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Media hooks provider class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Provider_MediaHooks extends Bandstand_AbstractProvider {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'after_setup_theme',            array( $this, 'register_image_sizes' ) );
		add_filter( 'wp_image_editors',             array( $this, 'register_image_editors' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'prepare_audio_attachment_for_js' ), 10, 3 );
	}

	/**
	 * Register new image sizes.
	 *
	 * @since 1.0.0
	 */
	public function register_image_sizes() {
		add_image_size( 'bandstand-thumbnail',      480, 480, array( 'center', 'top' ) );
		add_image_size( 'bandstand-thumbnail-16x9', 480, 270, array( 'center', 'top' ) );
	}

	/**
	 * Register custom image editors.
	 *
	 * @since 1.0.0
	 *
	 * @param array $editors Array of image editors.
	 * @return array
	 */
	public function register_image_editors( $editors ) {
		array_unshift( $editors, 'Bandstand_Image_Editor_GD' );
		array_unshift( $editors, 'Bandstand_Image_Editor_Imagick' );

		return $editors;
	}

	/**
	 * Add audio metadata to attachment response objects.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $response Attachment data to send as JSON.
	 * @param WP_Post $attachment Attachment object.
	 * @param array   $meta Attachment meta.
	 * @return array
	 */
	public function prepare_audio_attachment_for_js( $response, $attachment, $meta ) {
		if ( 'audio' !== $response['type'] ) {
			return $response;
		}

		if ( empty( $response['meta'] ) ) {
			$response['meta'] = array();
		}

		$response['meta'] = wp_parse_args( $response['meta'], $meta );

		return $response;
	}
}
