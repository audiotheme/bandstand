<?php
/**
 * Venue post.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Venue post class.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_Post_Venue extends Bandstand_Post_AbstractPost {
	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const POST_TYPE = 'bandstand_venue';

	/**
	 * Name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $name = '';

	/**
	 * Address.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $address = '';

	/**
	 * City.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $city = '';

	/**
	 * Region/state.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $region = '';

	/**
	 * Postal code.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $postal_code = '';

	/**
	 * Country.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $country = '';

	/**
	 * Phone.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $phone = '';

	/**
	 * Website URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $website_url = '';

	/**
	 * Gig count.
	 *
	 * @since 1.0.0
	 * @var integer
	 */
	public $gig_count = 0;

	/**
	 * Latitude.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $latitude = '';

	/**
	 * Longitude.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $longitude = '';

	/**
	 * Time zone id.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $timezone_id = '';

	/**
	 * Contact name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $contact_name = '';

	/**
	 * Contact email.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $contact_email = '';

	/**
	 * Contact phone.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $contact_phone = '';

	/**
	 * Notes.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $notes = '';

	/**
	 * Retrieve the name.
	 *
	 * This should be the same as the post title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Whether an address has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_address() {
		return ! empty( $this->address );
	}

	/**
	 * Retrieve the address.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_address() {
		return $this->address;
	}

	/**
	 * Whether a city has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_city() {
		return ! empty( $this->city );
	}

	/**
	 * Retrieve the ciy.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_city() {
		return $this->city;
	}

	/**
	 * Whether a country has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_country() {
		return ! empty( $this->country );
	}

	/**
	 * Retrieve the country.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_country() {
		return $this->country;
	}

	/**
	 * Retrieve the number of gigs associated with the venue.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_gig_count() {
		$count = null;

		if ( ! empty( $this->gig_count ) ) {
			$count = $this->gig_count;
		}

		if ( null === $count ) {
			$count = $this->update_gig_count();
		}

		return absint( $count );
	}

	/**
	 * Whether the latitude has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_latitude() {
		return ! empty( $this->latitude );
	}

	/**
	 * Retrieve the latitude.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_latitude() {
		return $this->latitude;
	}

	/**
	 * Whether the longitude has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_longitude() {
		return ! empty( $this->longitude );
	}

	/**
	 * Retrieve the longitude.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_longitude() {
		return $this->longitude;
	}

	/**
	 * Whether the phone number has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_phone() {
		return ! empty( $this->phone );
	}

	/**
	 * Retrieve the phone number.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_phone() {
		return $this->phone;
	}

	/**
	 * Whether the postal code has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_postal_code() {
		return ! empty( $this->postal_code );
	}

	/**
	 * Retrieve the postal code.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_postal_code() {
		return $this->postal_code;
	}

	/**
	 * Whether the region has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_region() {
		return ! empty( $this->region );
	}

	/**
	 * Retrieve the region.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_region() {
		return $this->region;
	}

	/**
	 * Retrieve the time zone.
	 *
	 * @since 1.0.0
	 *
	 * @return DateTimeZone
	 */
	public function get_timezone() {
		return new DateTimeZone( $this->get_timezone_id() );
	}

	/**
	 * Whether the venue has a timezone.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_timezone_id() {
		return ! empty( $this->timezone_id );
	}

	/**
	 * Retrieve the timezone identifier.
	 *
	 * @todo Test this with manual offsets.
	 *
	 * @since 1.0.0
	 *
	 * @link http://php.net/manual/en/timezones.php
	 *
	 * @return string Timezone identifier.
	 */
	public function get_timezone_id() {
		// Retrieve from post meta.
		if ( $this->has_timezone_id() ) {
			$timezone_id = $this->timezone_id;
		}

		// Fall back to the site's timezone.
		if ( empty( $timezone_id ) ) {
			$timezone_id = $this->get_site_timezone_id();
		}

		return $timezone_id;
	}

	/**
	 * Retrieve the site's time zone.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_site_timezone_id() {
		return get_option( 'timezone_string' );
	}

	/**
	 * Whether the website URL has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function has_website_url() {
		return ! empty( $this->website_url );
	}

	/**
	 * Retrieve the website URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_website_url() {
		return $this->website_url;
	}

	/**
	 * Update the number of gigs associated with the venue.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $count Optional. Number gigs assigned to the venue.
	 * @return int
	 */
	public function update_gig_count( $count = null ) {
		global $wpdb;

		if ( null === $count ) {
			$sql = $wpdb->prepare(
				"SELECT count( * )
				FROM $wpdb->posts p
				INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
				WHERE p.post_type = 'bandstand_gig' AND pm.meta_key = 'bandstand_venue_id' AND pm.meta_value = %d",
				$this->ID
			);

			$count = $wpdb->get_var( $sql );
		}

		update_post_meta( $this->ID, 'bandstand_gig_count', absint( $count ) );

		return $count;
	}
}
