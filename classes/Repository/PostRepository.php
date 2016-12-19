<?php
/**
 * Post repository.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Post repository class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Repository_PostRepository {
	/**
	 * Post field aliases.
	 *
	 * @var array
	 */
	protected $aliases = array(
		'ID'    => 'ID',
		'name'  => 'post_title',
		'title' => 'post_title',
	);

	/**
	 * Save a post.
	 *
	 * @since 1.0.0
	 *
	 * @todo Rename Post to CPT or something like it.
	 * @todo Create a CPT interface.
	 *
	 * @param  string         $type   Type of post.
	 * @param  Bandstand_Post $object Post object or array of data.
	 * @return Bandstand_Post
	 */
	public function save( $type, $object ) {
		if ( is_array( $object ) ) {
			$object = call_user_func( 'get_bandstand_' . $type, $object );
		}

		$post_id = $this->save_post( $object );
		$this->save_metadata( $post_id, $object->to_array() );

		return call_user_func( 'get_bandstand_' . $type, $post_id );
	}

	/**
	 * Create or update a post object.
	 *
	 * @since 1.0.0
	 *
	 * @param  array|Bandstand_Post $object Post data.
	 * @return int Post ID.
	 */
	protected function save_post( $object ) {
		$data = $object->to_array();

		// Copy fields that are post properties.
		$post = array_intersect_key( $data, get_class_vars( 'WP_Post' ) );

		// Merge aliased properties.
		foreach ( $this->aliases as $alias => $field ) {
			if ( isset( $data[ $alias ] ) ) {
				$post[ $field ] = $data[ $alias ];
			}
		}

		// Create or update the post container.
		if ( empty( $post['ID'] ) ) {
			$post['post_type'] = $object::POST_TYPE;
			$post_id = wp_insert_post( $post );
		} else {
			$post_id = wp_update_post( $post );
		}

		return $post_id;
	}

	/**
	 * Update post metadata.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $post_id Post ID.
	 * @param array $data    Metadata.
	 */
	protected function save_metadata( $post_id, $data ) {
		// Remove post fields.
		$metadata = array_diff_key( $data, get_class_vars( 'WP_Post' ) );

		// Remove aliased fields.
		$metadata = array_diff_key( $metadata, $this->aliases );

		foreach ( $metadata as $name => $value ) {
			update_post_meta( $post_id, 'bandstand_' . $name, $value );
		}
	}
}
