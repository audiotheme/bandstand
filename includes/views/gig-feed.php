<?php
/**
 * Gigs feed functions.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Retrieve gig information in markup suitable for a RSS description.
 *
 * @since 1.0.0
 * @uses get_bandstand_venue_vcard_rss()
 *
 * @param int|object $post Optional post ID or object. Default is global $post object.
 * @return string
 */
function get_bandstand_gig_rss_description( $post = null ) {
	$gig = get_bandstand_gig( $post );

	$output  = '<strong>' . $gig->format_start_date( 'l, F j, Y', ' @ g:i a' ) . '</strong>';
	$output .= $gig->has_venue() ? get_bandstand_venue_vcard( $gig->get_venue()->ID, array( 'container' => 'div' ) ) : '';
	$output .= empty( $gig->post_excerpt ) ? '' : wpautop( $gig->post_excerpt );

	return $output;
}

/**
 * Retrieve venue vCard markup suitable for use in an RSS feed.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post Venue post ID or object.
 * @return string Venue vCard.
 */
function get_bandstand_venue_vcard_rss( $post ) {
	$venue = get_bandstand_venue( $post );

	$output = '';

	$url = $venue->has_website_url() ? ' rdf:about="' . esc_url( $venue->get_website_url() ) . '"' : '';
	$output .= sprintf( '<v:vCard%s">', $url );
		$output .= '<v:fn>' . esc_html( $venue->get_name() ) . '</v:fn>';

		$locality  = $venue->has_city() ? $venue->get_city() : '';
		$locality .= $venue->has_city() && $venue->has_region() ? ', ' : '';
		$locality .= $venue->has_region() ? $venue->get_region() : '';

		$address  = '';
		$address .= $venue->has_address() ? '<v:street-address>' . esc_html( $venue->get_address() ) . '</v:street-address>' : '';
		$address .= empty( $locality ) ? '' : '<v:locality>' . esc_html( $locality ) . '</v:locality>, ';
		$address .= $venue->has_postal_code() ? '<v:postal-code>' . esc_html( $venue->get_postal_code() ) . '</v:postal-code>' : '';
		$address .= $venue->has_country() ? '<v:country-name>' . $venue->get_country() . '</v:country-name>' : '';

	if ( ! empty( $address ) ) {
		$output .= '<v:adr><rdf:Description>' . $address . '</rdf:Description></v:adr>';
	}

		$output .= $venue->has_phone() ? '<v:tel><rdf:Description><rdf:value>' . $venue->get_phone() . '</rdf:value></rdf:Description></v:tel>' : '';
	$output .= '</v:VCard>';

	return $output;
}

/**
 * Retrieve a venue's location suitable for an iCal feed.
 *
 * @since 1.0.0
 *
 * @param int|object $post Venue post ID or object.
 * @return string Venue iCal vCard.
 */
function get_bandstand_venue_location_ical( $post = null ) {
	$venue = get_bandstand_venue( $post );

	$output = $venue->get_name();

	$address = array();
	if ( $venue->has_address() ) {
		$address[] = $venue->get_address();
	}

	$locality  = $venue->has_city() ? $venue->get_city() : '';
	$locality .= $venue->has_city() && $venue->has_region() ? ', ' : '';
	$locality .= $venue->has_region() ? $venue->get_region() : '';
	if ( ! empty( $locality ) ) {
		$address[] = $locality;
	}

	if ( $venue->has_country() ) {
		$address[] = $venue->get_country();
	}

	if ( $venue->has_postal_code() ) {
		$address[] = $venue->get_postal_code();
	}

	if ( ! empty( $address ) ) {
		$output .= ', ' . join( $address, ', ' );
	}

	return escape_ical_text( $output );
}

if ( ! function_exists( 'escape_ical_text' ) ) :
/**
 * Sanitize text for inclusion in an iCal feed.
 *
 * @param string $text String to sanitize.
 * @return string
 */
function escape_ical_text( $text ) {
	$search = array( '\\', ';', ',', "\n", "\r" );
	$replace = array( '\\\\', '\;', '\,', ' ', ' ' );

	return str_replace( $search, $replace, $text );
}
endif;
