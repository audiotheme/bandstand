<?php
/**
 * Gig post.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Gig post class.
 *
 * @todo Document the way all these work together.
 * - Start date
 * - Start time
 * - End date
 * - End time
 * - Time zones
 * - All-day events
 *
 * If a gig doesn't have its own `timezone_id` meta data:
 * - No time or venue: time zone is the same as the site.
 * - With a time, but no venue: time zone is the same as the site.
 * - No time, but have a venue: time zone is the venue time.
 * - With time and venue: time zone is the venue time.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_Post_Gig extends Bandstand_Post_AbstractPost {
	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const POST_TYPE = 'bandstand_gig';

	/**
	 * Start date.
	 *
	 * @since 1.0.0
	 * @var string Formatted as Y-m-d.
	 */
	public $start_date;

	/**
	 * Start time.
	 *
	 * @since 1.0.0
	 * @var string Formatted as H:i:s.
	 */
	public $start_time;

	/**
	 * End date.
	 *
	 * @since 1.0.0
	 * @var string Formatted as Y-m-d.
	 */
	public $end_date;

	/**
	 * End time.
	 *
	 * @since 1.0.0
	 * @var string Formatted as H:i:s.
	 */
	public $end_time;

	/**
	 * Whether the event is all-day.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $is_all_day = false;

	/**
	 * Venue ID.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $venue_id;

	/**
	 * Retrieve the start date.
	 *
	 * @since 1.0.0
	 *
	 * @return string Date in Y-m-d format.
	 */
	public function get_start_date() {
		return $this->start_date;
	}

	/**
	 * Whether the event is scheduled for all day.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_all_day() {
		return (bool) $this->is_all_day || $this->has_post() && $this->get_post()->is_all_day;
	}

	/**
	 * Whether the start time has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_start_time() {
		return ! empty( $this->get_start_time() );
	}

	/**
	 * Retrieve the start time.
	 *
	 * @since 1.0.0
	 *
	 * @return string Time in H:i:s format.
	 */
	public function get_start_time() {
		$start_time = $this->start_time;

		if ( $this->is_all_day() ) {
			$start_time = '00:00:00';
		}

		return $start_time;
	}

	/**
	 * Retrieve a DateTime object for the start date.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $timezone_id Optional. A time zone identifier. Defaults to the event's local time zone.
	 * @return DateTime
	 */
	public function get_start_datetime( $timezone_id = '' ) {
		$date     = trim( $this->get_start_date() . ' ' . $this->get_start_time() );
		$timezone = $this->get_timezone();

		if ( ! empty( $timezone_id ) ) {
			$timezone = new DateTimeZone( $timezone_id );
		}

		return new DateTime( $date, $timezone );
	}

	/**
	 * Format the start date and/or time for display.
	 *
	 * Convenience wrapper around Bandstand_Post_Gig::format_date_time().
	 *
	 * @since 1.0.0
	 *
	 * @param  string $date_format Optional. PHP date format.
	 * @param  string $time_format Optional. PHP time format.
	 * @param  array  $args {
	 *     Optional. An array of arguments.
	 *
	 *     @type string $empty_time String to display when the time component isn't passed.
	 *     @type string $format     Sprintf template to control output. %1$s is the date. %2$s is the time. Defaults to '%1$s %2$s'.
	 *     @type bool   $translate  Whether to translate the date string. Defaults to true.
	 *     @type bool   $utc        Whether to display in UTC time. Defaults to false.
	 * }
	 * @return string
	 */
	public function format_start_date( $date_format = 'c', $time_format = '', $args = array() ) {
		return $this->format_date_time(
			$this->get_start_date(),
			$this->get_start_time(),
			$date_format,
			$time_format,
			$args
		);
	}

	/**
	 * Retrieve the end date.
	 *
	 * @since 1.0.0
	 *
	 * @return string Date in Y-m-d format.
	 */
	public function get_end_date() {
		$end_date = $this->end_date;

		if ( empty( $end_date ) && $this->is_all_day() ) {
			$end_date = $this->start_date;
		}

		return $end_date;
	}

	/**
	 * Whether the end time has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_end_time() {
		return ! empty( $this->get_end_time() );
	}

	/**
	 * Retrieve the end time.
	 *
	 * @since 1.0.0
	 *
	 * @return string Time in H:i:s format.
	 */
	public function get_end_time() {
		$end_time = $this->end_time;

		if ( $this->is_all_day() ) {
			$end_time = '23:59:59';
		}

		return $end_time;
	}

	/**
	 * Retrieve a DateTime object for the end date.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $timezone_id Optional. A time zone identifier. Defaults to the event's local time zone.
	 * @return DateTime
	 */
	public function get_end_datetime( $timezone_id = '' ) {
		$date     = trim( $this->get_end_date() . ' ' . $this->get_end_time() );
		$timezone = $this->get_timezone();

		if ( ! empty( $timezone_id ) ) {
			$timezone = new DateTimeZone( $timezone_id );
		}

		return new DateTime( $date, $timezone );
	}

	/**
	 * Format the start date and/or time for display.
	 *
	 * Convenience wrapper around Bandstand_Post_Gig::format_date_time().
	 *
	 * @since 1.0.0
	 *
	 * @param  string $date_format Optional. PHP date format.
	 * @param  string $time_format Optional. PHP time format.
	 * @param  array  $args {
	 *     Optional. An array of arguments.
	 *
	 *     @type string $empty_time String to display when the time component isn't passed.
	 *     @type string $format     Sprintf template to control output. %1$s is the date. %2$s is the time. Defaults to '%1$s %2$s'.
	 *     @type bool   $translate  Whether to translate the date string. Defaults to true.
	 *     @type bool   $utc        Whether to display in UTC time. Defaults to false.
	 * }
	 * @return string
	 */
	public function format_end_date( $date_format = 'c', $time_format = '', $args = array() ) {
		return $this->format_date_time(
			$this->get_end_date(),
			$this->get_end_time(),
			$date_format,
			$time_format,
			$args
		);
	}

	/**
	 * Whether the event has a time zone.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_timezone() {
		return $this->has_venue() || ( $this->has_post() && isset( $this->get_post()->bandstand_timezone_id ) );
	}

	/**
	 * Retrieve the timezone.
	 *
	 * @since 1.0.0
	 *
	 * @return DateTimeZone Time zone.
	 */
	public function get_timezone() {
		return new DateTimeZone( $this->get_timezone_id() );
	}

	/**
	 * Retrieve the time zone identifier.
	 *
	 * Events inherit the time zone of the connected venue, but may provide an
	 * override in a `timezone_id` meta field. Otherwise, they fall back to the
	 * site's time zone.
	 *
	 * @todo Test this with manual offsets.
	 *
	 * @since 1.0.0
	 *
	 * @link http://php.net/manual/en/timezones.php
	 *
	 * @return string Time zone identifier.
	 */
	public function get_timezone_id() {
		// Retrieve from post meta.
		if ( $this->has_post() && isset( $this->get_post()->bandstand_timezone_id ) ) {
			$timezone = $this->get_post()->bandstand_timezone_id;
		}

		// Retrieve from the venue.
		if ( empty( $timezone ) && $this->has_venue() ) {
			$timezone = $this->get_venue()->get_timezone_id();
		}

		// Fall back to the site's timezone.
		if ( empty( $timezone ) ) {
			$timezone = $this->get_site_timezone_id();
		}

		return $timezone;
	}

	/**
	 * Whether a venue has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_venue() {
		return ! empty( $this->venue_id );
	}

	/**
	 * Retrieve the venue.
	 *
	 * @since 1.0.0
	 *
	 * @return Bandstand_Post_Venue
	 */
	public function get_venue() {
		return get_bandstand_venue( $this->venue_id );
	}

	/**
	 * Set the venue.
	 *
	 * The venue GUID is synchronized in Bandstand_PostType_Gig::sync_venue_guid(),
	 * so that gig/venue connections can be remapped after an import.
	 *
	 * @since 1.0.0
	 *
	 * @param  int|WP_Post|Bandstand_Post_Venue $venue Venue ID, post object or venue object.
	 * @return Bandstand_Post_Gig
	 */
	public function set_venue( $venue ) {
		if ( ! $this->has_post() ) {
			return $this;
		}

		$venue = get_bandstand_venue( $venue );
		update_post_meta( $this->ID, 'bandstand_venue_id', $venue->ID );
		$venue->update_gig_count();
		$data = array_merge( $this->to_array(), array( 'venue_id' => $venue->ID ) );

		return new Bandstand_Post_Gig( $data );
	}

	/**
	 * Generate the time used for sorting events.
	 *
	 * Sorting events requires a common time zone.
	 *
	 * If a time zone isn't specified, this will likely be off by up to 24 hours.
	 *
	 * @todo Consider storing this as a UNIX timestamp in the menu_order field?
	 * @todo This will be synchronized with the start time. Allow for a user-specified override.
	 *
	 * @since 1.0.0
	 *
	 * @return string Datetime in Y-m-d H:i:s format.
	 */
	public function generate_sort_time() {
		$datetime = $this->get_start_datetime();
		$datetime->setTimezone( new DateTimeZone( 'UTC' ) );
		return $datetime->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Generate the time that determines whether the event should be considered
	 * "upcoming".
	 *
	 * Attempts to determine the end of the day in local time. Uses the end date
	 * if specified, otherwise, falls back to the start date.
	 *
	 * If a time zone isn't specified, this will likely be off by up to 24 hours.
	 *
	 * @todo This will be synchronized with the end time. Allow for a user-specified override.
	 *
	 * @since 1.0.0
	 *
	 * @return string Datetime in Y-m-d H:i:s format.
	 */
	public function generate_upcoming_until_time() {
		if ( empty( $this->get_end_date() ) ) {
			$datetime = $this->get_start_datetime();
		} else {
			$datetime = $this->get_end_datetime();
		}

		$hours_to_end_of_day = 24 - (int) $datetime->format( 'H' );
		$datetime->setTimezone( new DateTimeZone( 'UTC' ) );

		return $datetime->modify( sprintf( '+%d hours', $hours_to_end_of_day ) )
			->format( 'Y-m-d H:i:s' );
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
	 * Format the date and/or time for display.
	 *
	 * Accepts date and time parameters separately due to the time not always
	 * being available.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $date        Optional. Date string.
	 * @param  string $time        Optional. Time string.
	 * @param  string $date_format Optional. PHP date format.
	 * @param  string $time_format Optional. PHP time format.
	 * @param  array  $args {
	 *     Optional. An array of arguments.
	 *
	 *     @type string $empty_time String to display when the time component isn't passed.
	 *     @type string $format     Sprintf template to control output. %1$s is the date. %2$s is the time. Defaults to '%1$s %2$s'.
	 *     @type bool   $translate  Whether to translate the date string. Defaults to true.
	 *     @type bool   $utc        Whether to display in UTC time. Defaults to false.
	 * }
	 * @return string
	 */
	protected function format_date_time( $date = '', $time = '', $date_format = 'c', $time_format = '', $args = array() ) {
		$args = wp_parse_args( $args, array(
			'empty_time' => '', // Unused at the moment.
			'format'     => '%1$s %2$s',
			'translate'  => true,
			'utc'        => false,
		) );

		$datetime_string = trim( $date . ' ' . $time );
		$format = $date_format;

		// Determine the output format.
		if ( ! empty( $time ) ) {
			$format = trim( sprintf(
				$args['format'],
				$date_format,
				$time_format
			) );
		} else {
			// ISO 8601 without time or time zone component.
			$format = 'c' === $date_format ? 'Y-m-d' : $date_format;
		}

		/*
		if ( $args['utc'] ) {
			$datetime = new DateTime( $datetime_string, $this->get_timezone() );
			$utc_format = 'Y-m-d';

			// Only adjust the time zone if the time component is available.
			if ( ! empty( $time ) ) {
				$utc_format = 'c';
				$datetime->setTimezone( new DateTimeZone( 'UTC' ) );
			}

			$datetime_string = $datetime->format( $utc_format );
		}
		*/

		if ( ! $args['utc'] ) {
			date_default_timezone_set( $this->get_timezone_id() );
		}

		$timestamp = strtotime( $datetime_string );
		if ( $args['translate'] ) {
			$result = date_i18n( $format, $timestamp );
		} else {
			$result = date( $format, $timestamp );
		}

		date_default_timezone_set( 'UTC' );

		return $result;
	}
}
