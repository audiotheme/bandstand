<?php
/**
 * Venue template tags and functions.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Retrieve a gig.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return Bandstand_Post_Gig
 */
function get_bandstand_gig( $post = null ) {
	return bandstand()->post_factory->make( 'gig', $post );
}

/**
 * Save a gig.
 *
 * @since 1.0.0
 *
 * @param  Bandstand_Post_Gig|array $gig Gig object or array of gig data.
 * @return Bandstand_Post_Gig
 */
function save_bandstand_gig( $gig ) {
	$repository = new Bandstand_Repository_PostRepository();
	return $repository->save( 'gig', $gig );
}

/**
 * Retrieve a gig's title.
 *
 * If the title is empty, attempt to construct one from the venue name or
 * fallback to the gig date.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return string
 */
function get_bandstand_gig_title( $post = null ) {
	$gig = get_bandstand_gig( $post );

	$title = empty( $gig->post_title ) ? '' : $gig->post_title;

	if ( empty( $title ) ) {
		if ( $gig->has_venue() ) {
			$title = $gig->get_venue()->get_name();
		} else {
			$title = $gig->format_start_date( 'F j, Y' );
		}
	}

	return apply_filters( 'get_bandstand_gig_title', $title, $gig );
}

/**
 * Display the link to the current gig.
 *
 * @since 1.0.0
 *
 * @param array $args Optional. Passed to get_bandstand_gig_link().
 */
function the_bandstand_gig_link( $args = array() ) {
	echo get_bandstand_gig_link( null, $args );
}

/**
 * Retrieve the link to the current gig.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @param  array              $args {
 *     Optional. Override the defaults and modify the output structure.
 *
 *     @type string $before      Optional. Content to prepend to the link.
 *     @type string $after       Optional. Content to append to the link.
 *     @type string $before_link Optional. Content to prepend inside the <a> tag.
 *                               Defaults to <span class="summary" itemprop="name">
 *     @type string $after_link  Optional. Cotnent to append inside the <a> tag.
 *                               Defaults to </span>.
 * }
 * @return string
 */
function get_bandstand_gig_link( $post = null, $args = array() ) {
	$gig = get_bandstand_gig( $post );

	$args = wp_parse_args( $args, array(
		'before'      => '',
		'after'       => '',
		'before_link' => '<span class="summary" itemprop="name">',
		'after_link'  => '</span>',
	) );

	$html  = $args['before'];
	$html .= '<a href="' . esc_url( get_permalink( $gig->ID ) ) . '" class="url uid" itemprop="url">';
	$html .= $args['before_link'] . get_bandstand_gig_title( $post ) . $args['after_link'];
	$html .= '</a>';
	$html .= $args['after'];

	return $html;
}

/**
 * Retrieve a gig's location (city, region, country).
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return string                   Location with microformat markup.
 */
function get_bandstand_gig_location( $post = null ) {
	$gig = get_bandstand_gig( $post );

	$location = '';
	if ( $gig->has_venue() ) {
		$location = get_bandstand_venue_location( $gig->get_venue()->ID );
	}

	return $location;
}

/**
 * Display or retrieve the current gig's description.
 *
 * @since 1.0.0
 *
 * @param string $before Optional. Content to prepend to the description.
 * @param string $after  Optional. Content to append to the description.
 */
function the_bandstand_gig_description( $before = '', $after = '' ) {
	$description = get_bandstand_gig_description();

	if ( ! empty( $description ) ) {
		echo $before . wpautop( $description ) . $after;
	}
}

/**
 * Retrieve a gig's description.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return string
 */
function get_bandstand_gig_description( $post = null ) {
	return get_post( $post )->post_excerpt;
}

/**
 * Whether a gig has ticket meta.
 *
 * @since 1.0.0
 *
 * @param  string             $key  Check for a particular type of meta. Defaults to any.
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return bool
 */
function bandstand_gig_has_ticket_meta( $key = '', $post = null ) {
	$gig = get_bandstand_gig( $post );

	$keys = array(
		'price' => 'bandstand_tickets_price',
		'url'   => 'bandstand_tickets_url',
	);

	if ( $key && ! isset( $keys[ $key ] ) ) {
		return false;
	} elseif ( $key ) {
		// Reset the keys array with a single value.
		$keys = array( $key => $keys[ $key ] );
	}

	foreach ( $keys as $key ) {
		if ( get_post_meta( $gig->ID, $key, true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Retrieve a gig's ticket price.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return string
 */
function get_bandstand_gig_tickets_price( $post = null ) {
	$gig = get_bandstand_gig( $post );
	return get_post_meta( $gig->ID, 'bandstand_tickets_price', true );
}

/**
 * Retrieve a gig's ticket url.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return string
 */
function get_bandstand_gig_tickets_url( $post = null ) {
	$gig = get_bandstand_gig( $post );
	return get_post_meta( $gig->ID, 'bandstand_tickets_url', true );
}

/**
 * Get a link to add a gig to Google Calendar.
 *
 * @since 1.0.0
 *
 * @todo Need to add the artists' name to provide context in Google Calendar.
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return string
 */
function get_bandstand_gig_gcal_link( $post = null ) {
	$gig = get_bandstand_gig( $post );

	$date = $gig->format_start_date( 'Ymd', '', array( 'utc' => true ) );
	$time = $gig->format_start_date( '', 'His', array( 'utc' => true ) );

	$dtstart  = $date;
	$dtstart .= empty( $time ) ? '' : 'T' . $time . 'Z';

	$location = '';
	if ( $gig->has-venue() ) {
		$venue = $gig->get_venue();

		$location  = $venue->get_name();
		$location .= $venue->has_address() ? ', ' . esc_html( $venue->get_address() ) : '';
		$location .= $venue->has_city() ? ', ' . $venue->get_city() : '';
		$location .= ( ! empty( $location ) && $venue->has_region() ) ? ', ' : '';
		$location .= $venue->has_region() ? $venue->get_region() : '';

		if ( $venue->has_country() ) {
			$location .= empty( $location ) ? '' : ', ';
			$location .= $venue->get_country();
		}
	}

	$args = array(
		'action'   => 'TEMPLATE',
		'text'     => rawurlencode( wp_strip_all_tags( get_bandstand_gig_title() ) ),
		'dates'    => $dtstart . '/' . $dtstart,
		'details'  => rawurlencode( wp_strip_all_tags( get_bandstand_gig_description() ) ),
		'location' => rawurlencode( $location ),
		'sprop'    => rawurlencode( home_url( '/' ) ),
	);

	$link = add_query_arg( $args, 'https://www.google.com/calendar/event' );

	return $link;
}

/**
 * Display a link to add a gig to Google Calendar.
 *
 * @since 1.0.0
 */
function the_bandstand_gig_gcal_link() {
	echo esc_url( get_bandstand_gig_gcal_link() );
}

/**
 * Get the URL for a gig's iCal endpoint.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @return string
 */
function get_bandstand_gig_ical_link( $post = null ) {
	$post = get_post( $post );
	$permalink = get_option( 'permalink_structure' );

	$ical_link = get_permalink( $post );
	$ical_link = empty( $permalink ) ? add_query_arg( '', 'ical', $ical_link ) : trailingslashit( $ical_link ) . 'ical/';

	return $ical_link;
}

/**
 * Display the link to a gig's iCal endpoint.
 *
 * @since 1.0.0
 */
function the_bandstand_gig_ical_link() {
	echo esc_url( get_bandstand_gig_ical_link() );
}
