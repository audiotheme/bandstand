<?php
/**
 * Base post.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Base post class.
 *
 * @package Bandstand
 * @since   1.0.0
 */
abstract class Bandstand_Post_AbstractPost implements JsonSerializable {
	/**
	 * WordPress post ID.
	 *
	 * @var int
	 */
	public $ID = null;

	/**
	 * Title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $title = '';

	/**
	 * WordPress post object.
	 *
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * Constructor method.
	 *
	 * @param array $attributes Post attributes.
	 */
	public function __construct( $attributes = array() ) {
		$this->fill( $attributes );
	}

	/**
	 * Whether a property exists.
	 *
	 * @param string $name Property name or metadata key.
	 * @return bool
	 */
	public function __isset( $name ) {
		return isset( $this->get_post()->$name );
	}

	/**
	 * Proxy access to post properties and metadata.
	 *
	 * @param string $name Post property name or metadata key.
	 * @return mixed
	 */
	public function __get( $name ) {
		return $this->get_post()->$name;
	}

	/**
	 * Magic mutator.
	 *
	 * @param string $name  Property name.
	 * @param mixed  $value Property value.
	 */
	public function __set( $name, $value ) {
		// Prevent undeclared properties from being set.
	}

	/**
	 * Retrieve the title.
	 *
	 * This should be the same as the post title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Whether a post has been set.
	 *
	 * @return WP_Post
	 */
	public function has_post() {
		return ! empty( $this->post );
	}

	/**
	 * Retrieve the WP Post object.
	 *
	 * @return WP_Post
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * Set the underlying post object.
	 *
	 * @param  WP_Post $post Post object.
	 * @return $this
	 */
	public function set_post( WP_Post $post ) {
		$this->post = $post;
		return $this;
	}

	/**
	 * Convert the post to an array.
	 *
	 * Creates an array from the post's public properties.
	 *
	 * @return array
	 */
	public function to_array() {
		$result     = array();
		$properties = call_user_func( 'get_object_vars', $this );

		foreach ( $properties as $name => $value ) {
			if ( method_exists( $this, 'get_' . $name ) ) {
				$result[ $name ] = call_user_func( array( $this, 'get_' . $name ) );
			} else {
				$result[ $name ] = $value;
			}
		}

		return $result;
	}

	/**
	 * Serialize the model for use as JSON.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->to_array();
	}

	/**
	 * Retrieve default properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_default_properties() {
		return get_class_vars( self::get_called_class() );
	}

	/**
	 * Set properties.
	 *
	 * @param array $values Array of properties.
	 */
	protected function fill( $values = [] ) {
		$values = (array) $values;

		foreach ( array_keys( call_user_func( 'get_object_vars', $this ) ) as $key ) {
			if ( ! isset( $values[ $key ] ) ) {
				continue;
			}

			$this->set_property( $key, $values[ $key ] );
		}

		return $this;
	}

	/**
	 * Set a property value.
	 *
	 * @param string $name  Property name.
	 * @param mixed  $value Property value.
	 */
	protected function set_property( $name, $value ) {
		if ( method_exists( $this, 'set_' . $name ) ) {
			call_user_func( array( $this, 'set_' . $name ), $value );
		} else {
			$this->$name = $value;
		}

		return $this;
	}

	/**
	 * Shim for get_called_class() for PHP 5.2.
	 *
	 * @since 1.0.0
	 *
	 * @link https://github.com/WP-API/OAuth1/blob/bd6d869decc17d235e01a06ac061c6c75208e453/lib/class-wp-rest-client.php#L227-L241
	 *
	 * @return string Class name.
	 */
	protected static function get_called_class() {
		if ( function_exists( 'get_called_class' ) ) {
			return get_called_class();
		}

		// PHP 5.2 only
		$backtrace = debug_backtrace();
		if ( 'call_user_func' === $backtrace[2]['function'] ) {
			return $backtrace[2]['args'][0][0];
		}

		return $backtrace[2]['class'];
	}
}
