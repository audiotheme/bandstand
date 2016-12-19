<?php
/**
 * Videos REST controller.
 *
 * @package   Bandstand\Videos
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Videos REST controller class.
 *
 * @package Bandstand\Videos
 * @since   1.0.0
 */
class Bandstand_REST_VideosController extends Bandstand_REST_AbstractPostsController {
	/**
	 * Update the values of additional fields added to a data object.
	 *
	 * @param WP_Post         $post    Post object.
	 * @param WP_REST_Request $request Request instance.
	 */
	protected function update_additional_fields_for_object( $post, $request ) {
		$data = array_merge( $request->get_params(), array( 'ID' => $post->ID ) );
		save_bandstand_video( $data );
	}

	/**
	 * Prepare a single post output for response.
	 *
	 * @param  WP_Post         $post    Post object.
	 * @param  WP_REST_Request $request HTTP request instance.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $post, $request ) {
		$video = get_bandstand_video( $post );

		$data = array(
			'id'       => $video->ID,
			'duration' => $video->get_duration(),
			'embed'    => $video->get_html(),
			'title'    => $video->get_title(),
			'url'      => $video->get_url(),
		);

		if ( 'edit' === $request['context'] ) {
			$data['status'] = $post->post_status;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $post ) );

		return $response;
	}

	/**
	 * Get the video schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_item_schema() {
		return array(
			'$schema'    => 'http://json-schema.org/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'duration' => array(
					'description' => esc_html__( 'The video duration.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'embed'    => array(
					'description' => esc_html__( 'Video embed HTML.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'id'       => array(
					'description' => esc_html__( 'Unique identifier for the video.', 'bandstand' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => true,
				),
				'title'    => array(
					'description' => esc_html__( 'The title of the video.', 'bandstand' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit', 'embed' ),
				),
				'url'      => array(
					'description' => esc_html__( 'The URL to the video file or service.', 'bandstand' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
				),
			),
		);
	}
}
