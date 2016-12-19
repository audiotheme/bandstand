<?php
/**
 * Administration AJAX provider.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Administration AJAX provider class.
 *
 * @package Bandstand\Administration
 * @since   1.0.0
 */
class Bandstand_AJAX_Admin {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_bandstand_ajax_insert_term', array( $this, 'insert_term' ) );
	}

	/**
	 * AJAX callback to insert a new term.
	 *
	 * @since 1.0.0
	 */
	public function insert_term() {
		$response       = array();
		$taxonomy       = sanitize_key( $_POST['taxonomy'] );
		$is_valid_nonce = isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'add-term_' . $taxonomy );

		if ( ! $is_valid_nonce ) {
			$response['message'] = __( 'Unauthorized request.', 'bandstand' );
			wp_send_json_error( $response );
		}

		$term      = empty( $_POST['term'] ) ? '' : sanitize_text_field( $_POST['term'] );
		$term_data = wp_insert_term( $term, $taxonomy );

		if ( is_wp_error( $term_data ) ) {
			$response['message'] = $term_data->get_error_message();
			wp_send_json_error( $response );
		}

		$response['html'] = sprintf(
			'<li><label><input type="checkbox" name="bandstand_post_terms[%s][]" value="%d" checked="checked"> %s</label></li>',
			esc_attr( $taxonomy ),
			absint( $term_data['term_id'] ),
			$term
		);

		wp_send_json_success( $response );
	}
}
