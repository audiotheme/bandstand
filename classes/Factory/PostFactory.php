<?php
/**
 * Post factory.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Post factory class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Factory_PostFactory {
	/**
	 * Make a post.
	 *
	 * @since 1.0.0
	 *
	 * @param  string             $type Custom post type..
	 * @param  int|WP_Post|string $post Optional. Post ID, post object, CPT slug, CPT object, or an array of attributes.
	 *                                  Defaults to the current post in the loop.
	 * @return mixed
	 */
	public function make( $type, $post = null ) {
		$classname = $this->get_classname_from_type( $type );

		if ( ! class_exists( $classname ) ) {
			return null;
		}

		return $this->get_custom_post_type( $classname, $post );
	}

	/**
	 * Determine the class name from the type.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $type Custom post type.
	 * @return string
	 */
	protected function get_classname_from_type( $type ) {
		return 'Bandstand_Post_' . ucfirst( str_replace( 'bandstand_', '', $type ) );
	}

	/**
	 * Get the CPT.
	 *
	 * @since 1.0.0
	 *
	 * @param  string             $classname Custom post type class name.
	 * @param  int|WP_Post|string $post      Optional. Post ID, post object, CPT slug, CPT object, or an array of attributes.
	 *                                       Defaults to the current post in the loop.
	 * @return mixed
	 */
	protected function get_custom_post_type( $classname, $post = null ) {
		$attributes = array();
		$data       = array();

		// Array of attributes.
		if ( is_array( $post ) ) {
			$data = $post;
			unset( $post );
		}

		// Use a post ID from the array if it exists.
		if ( ! empty( $data['ID'] ) ) {
			$post = $data['ID'];
		}

		// Fetch the post when an array of attributes wasn't passed.
		if ( empty( $data ) || ! empty( $post ) ) {
			$post       = $this->get_post( $post, $classname );
			$attributes = $this->merge_attributes( get_class_vars( $classname ), $post );
		}

		$object = new $classname( array_merge( $attributes, $data ) );

		if ( ! empty( $post ) ) {
			$object->set_post( $post );
		}

		return $object;
	}

	/**
	 * Retrieve a post object.
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed  $post      Post ID, post object, post slug, or an instance of $classname.
	 * @param  string $classname Class name.
	 * @return WP_Post
	 */
	protected function get_post( $post = null, $classname ) {
		if ( null === $post ) {
			$post = get_post();
		} elseif ( $post instanceof $classname ) {
			$post = $post->get_post();
		} elseif ( is_numeric( $post ) || ! is_string( $post ) ) {
			$post = get_post( $post );
		} elseif ( ! empty( $post ) ) {
			$post = get_page_by_path( $post, OBJECT, $classname::POST_TYPE );
		}

		return $post;
	}

	/**
	 * Merge post fields and metadata with an attributes array.
	 *
	 * @since 1.0.0
	 *
	 * @todo Allow for a metadata map.
	 *
	 * @param  array   $attributes Array of attributes.
	 * @param  WP_Post $post       Post object.
	 * @return array
	 */
	protected function merge_attributes( $attributes, $post ) {
		$field_map = array(
			'ID'    => 'ID',
			'name'  => 'post_title', // @todo Pass this in from elsewhere.
			'title' => 'post_title',
		);

		foreach ( $attributes as $name => $default ) {
			if ( isset( $field_map[ $name ] ) ) {
				$value = $post->{$field_map[ $name ]};
			} else {
				$value = get_post_meta( $post->ID, 'bandstand_' . $name, true );
			}

			$attributes[ $name ] = $value;
		}

		return $attributes;
	}
}
