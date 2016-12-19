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
 * Retrieve a venue.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug. Defaults to the current post.
 * @return Bandstand_Post_Venue
 */
function get_bandstand_venue( $post = null ) {
	if ( null === $post ) {
		$post = get_bandstand_gig()->get_venue();
	}

	return bandstand()->post_factory->make( 'venue', $post );
}

/**
 * Save a venue.
 *
 * @since 1.0.0
 *
 * @param  Bandstand_Post_Venue|array $venue Venue object or array of venue data.
 * @return Bandstand_Post_Venue
 */
function save_bandstand_venue( $venue ) {
	$repository = new Bandstand_Repository_PostRepository();
	return $repository->save( 'venue', $venue );
}

/**
 * Retrieve a venue by a property.
 *
 * The only field currently supported is the venue name.
 *
 * @since 1.0.0
 *
 * @param  string $field Field name.
 * @param  string $value Field value.
 * @return WP_Post|null
 */
function get_bandstand_venue_by( $field, $value ) {
	global $wpdb;

	$field = 'name';

	$venue_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID
		FROM $wpdb->posts
		WHERE post_type = 'bandstand_venue' AND post_title = %s",
		$value
	) );

	if ( ! $venue_id ) {
		return null;
	}

	return get_bandstand_venue( $venue_id );
}

/**
 * Display the link to the current venue's website.
 *
 * @since 1.0.0
 *
 * @param  array $args Optional. Passed to get_bandstand_venue_link().
 * @return string|null Null on failure or display. String when echo is false.
 */
function the_bandstand_gig_venue_link( $args = array() ) {
	$gig = get_bandstand_gig();

	if ( ! $gig->has_venue() ) {
		return;
	}

	echo get_bandstand_venue_link( $gig->get_venue()->ID, $args );
}

/**
 * Retrieve the link to a venue's website.
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
function get_bandstand_venue_link( $post, $args = array() ) {
	$venue = get_bandstand_venue( $post );

	if ( empty( $venue->post_title ) ) {
		return '';
	}

	$args = wp_parse_args( $args, array(
		'before'      => '',
		'after'       => '',
		'before_link' => '<span class="fn org" itemprop="name">',
		'after_link'  => '</span>',
	) );

	$html  = $args['before'];
	$html .= $venue->has_website_url() ? sprintf( '<a href="%s" class="url" itemprop="url">', esc_url( $venue->get_website_url() ) ) : '';
	$html .= $args['before_link'] . $venue->post_title . $args['after_link'];
	$html .= $venue->has_website_url() ? '</a>': '';
	$html .= $args['after'];

	return $html;
}

/**
 * Display the current venue with vCard markup.
 *
 * @since 1.0.0
 *
 * @param  array $args Optional. Passed to get_bandstand_venue_vcard().
 * @return string|null Null on failure or display. String when echo is false.
 */
function the_bandstand_venue_vcard( $args = array() ) {
	$gig = get_bandstand_gig();

	if ( ! $gig->has_venue() ) {
		return;
	}

	echo get_bandstand_venue_vcard( $gig->get_venue()->ID, $args );
}

/**
 * Retrieve a venue with vCard markup.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @param  array              $args {
 *     Optional. Override the defaults and modify the output structure.
 *
 *     @type string $container HTML tag to wrap the vCard. Defaults to 'dd'.
 * }
 * @return string
 */
function get_bandstand_venue_vcard( $post, $args = array() ) {
	$venue = get_bandstand_venue( $post );

	$args = wp_parse_args( $args, array(
		'container'         => 'dd',
		'show_country'      => true,
		'show_name'         => true,
		'show_name_link'    => true,
		'show_phone'        => true,
		'separator_address' => '<br>',
		'separator_country' => '<br>',
	) );

	$html  = '';

	if ( $args['show_name'] ) {
		$html .= $venue->has_website_url() && $args['show_name_link'] ? '<a href="' . esc_url( $venue->get_website_url() ) . '" class="url" itemprop="url">' : '';
		$html .= '<span class="venue-name fn org" itemprop="name">' . $venue->get_name() . '</span>';
		$html .= $venue->has_website_url() && $args['show_name_link'] ? '</a>' : '';
	}

	$address  = '';
	if ( $venue->has_address() ) {
		$address .= '<span class="street-address" itemprop="streetAddress">' . esc_html( $venue->get_address() ) . '</span>';
	}

	$region  = '';
	$region .= $venue->has_city() ? '<span class="locality" itemprop="addressLocality">' . esc_html( $venue->get_city() ) . '</span>' : '';
	$region .= $venue->has_city() && $venue->has_region() ? ', ' : '';
	$region .= $venue->has_region() ? '<span class="region" itemprop="addressRegion">' . esc_html( $venue->get_region() ) . '</span>' : '';
	$region .= $venue->has_postal_code() ? ' <span class="postal-code" itemprop="postalCode">' . esc_html( $venue->get_postal_code() ) . '</span>' : '';

	$address .= empty( $address ) || empty( $region ) ? '' : '<span class="sep sep-street-address">' . $args['separator_address'] . '</span>';
	$address .= empty( $region ) ? '' : '<span class="venue-location">' . $region . '</span>';

	if ( $venue->has_country() && $args['show_country'] && apply_filters( 'show_bandstand_venue_country', true ) ) {
		$country_class = esc_attr( 'country-name-' . sanitize_title_with_dashes( $venue->get_country() ) );

		$address .= empty( $address ) || empty( $venue->country ) ? '' : '<span class="sep sep-country-name ' . $country_class . '">' . $args['separator_country'] . '</span> ';
		$address .= $venue->has_country() ? '<span class="country-name ' . $country_class . '" itemprop="addressCountry">' . $venue->get_country() . '</span>' : '';
	}

	$html .= empty( $address ) ? '' : '<div class="venue-address adr" itemscope itemtype="http://schema.org/PostalAddress" itemprop="address">' . $address . '</div> ';

	if ( $args['show_phone'] ) {
		$html .= $venue->has_phone() ? '<span class="venue-phone tel" itemprop="telephone">' . $venue->get_phone() . '</span>' : '';
	}

	if ( ! empty( $html ) && ! empty( $args['container'] ) ) {
		$container_open = '<' . $args['container'] . ' class="location vcard" itemprop="location" itemscope itemtype="http://schema.org/EventVenue">';
		$container_close = '</' . $args['container'] . '>';

		$html = $container_open . $html . $container_close;
	}

	return $html;
}

/**
 * Retrieve a venue's address as a string.
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @param  array              $args Optional. Override the defaults and modify the output structure.
 * @return string
 */
function get_bandstand_venue_address( $post, $args = array() ) {
	$venue = get_bandstand_venue( $post );

	$address  = '';
	$address .= $venue->has_address() ? trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $venue->get_address() ) ) ) . ', ' : '';

	$address .= $venue->has_city() ? $venue->get_city() : '';
	$address .= $venue->has_city() && $venue->has_region() ? ', ' : '';
	$address .= $venue->has_region() ? $venue->get_region() : '';
	$address .= $venue->has_postal_code() ? ' ' . $venue->get_postal_code() : '';

	return $address;
}

/**
 * Retrieve a venue's location (city, region, country).
 *
 * @since 1.0.0
 *
 * @param  int|WP_Post|string $post Optional. Post ID, post object, or CPT slug.
 *                                  Defaults to the current post in the loop.
 * @param  array              $args Optional. Override the defaults and modify the output structure.
 * @return string
 */
function get_bandstand_venue_location( $post, $args = array() ) {
	$venue = get_bandstand_venue( $post );

	$location  = '';
	$location .= $venue->has_city() ? '<span class="locality">' . $venue->get_city() . '</span>' : '';
	$location .= $venue->has_city() && $venue->has_region() ? '<span class="sep sep-region">,</span> ' : '';
	$location .= $venue->has_region() ? '<span class="region">' . $venue->get_region() . '</span>' : '';

	if ( $venue->has_country() && apply_filters( 'show_bandstand_venue_country', true ) ) {
		$country_class = esc_attr( 'country-name-' . sanitize_title_with_dashes( $venue->get_country() ) );

		$location .= empty( $location ) ? '' :  '<span class="sep sep-country-name ' . $country_class . '">,</span> ';
		$location .= '<span class="country-name ' . $country_class . '">' . $venue->get_country() . '</span>';
	}

	return $location;
}

/**
 * Build a URL to a Google Map.
 *
 * @since 1.0.0
 *
 * @param  array $args Array of arguments.
 * @param  int   $post Optional. Venue ID or object.
 * @return string
 */
function get_bandstand_google_map_url( $args = array(), $post ) {
	$args = wp_parse_args( $args, array(
		'address' => '',
	) );

	// Get the current post and determine if it's a gig with a venue.
	if ( empty( $args['address'] ) && ( $gig = get_bandstand_gig() ) ) {
		if ( 'bandstand_gig' === get_post_type( $gig ) && $gig->has_venue() ) {
			$venue_id = $gig->get_venue()->ID;
		}
	}

	// Retrieve the address for the venue.
	if ( $venue_id ) {
		$venue = get_bandstand_venue( $venue_id );

		$args['address'] = get_bandstand_venue_address( $venue->ID );
		$args['address'] = empty( $args['address'] ) ? $venue->get_name() : $venue->get_name() . ', ' . $args['address'];
	}

	$url = add_query_arg( array(
		'q' => rawurlencode( $args['address'] ),
	), '//maps.google.com/maps' );

	return apply_filters( 'bandstand_google_map_url', $url, $args, $venue_id );
}

/**
 * Generate a Google Map iframe for an address or venue.
 *
 * If a venue ID is passed as the second parameter, it's address will supercede
 * the address argument in the $args array.
 *
 * If the address argument is left empty and the current post is a gig CPT and
 * it has a venue with an address, that is the address that will be used.
 *
 * @since 1.0.0
 *
 * @param array $args {
 *     Optional. Array of arguments.
 *
 *     @type string $address The address to send to Google.
 *     @type string $width   Width of the iframe. Defaults to 100%.
 *     @type string $height  Height of the iframe. Defaults to 300.
 * }
 * @param  int   $venue_id Optional. Venue ID.
 * @return string
 */
function get_bandstand_google_map_embed( $args = array(), $venue_id = 0 ) {
	$args = wp_parse_args( $args, array(
		'address'   => '',
		'width'     => '100%',
		'height'    => 300,
		'link_text' => __( 'Get Directions', 'bandstand' ),
		'format'    => '%1$s<p class="venue-map-link">%2$s</p>',
	) );

	// Get the current post and determine if it's a gig with a venue.
	if ( empty( $args['address'] ) && ( $gig = get_bandstand_gig() ) ) {
		if ( 'bandstand_gig' === get_post_type( $gig ) && $gig->has_venue() ) {
			$venue_id = $gig->get_venue()->ID;
		}
	}

	// Retrieve the address for the venue.
	if ( $venue_id ) {
		$venue = get_bandstand_venue( $venue_id );

		$args['address'] = get_bandstand_venue_address( $venue->ID );
		$args['address'] = empty( $args['address'] ) ? $venue->get_name() : $venue->get_name() . ', ' . $args['address'];
	}

	$args['embed_url'] = add_query_arg( array(
		'q'      => rawurlencode( $args['address'] ),
		'output' => 'embed',
		'key'    => bandstand()->modules['gigs']->get_google_maps_api_key(),
	), '//maps.google.com/maps' );

	$args['link_url'] = add_query_arg( 'q', urlencode( $args['address'] ), 'https://maps.google.com/maps' );

	$args = apply_filters( 'bandstand_google_map_embed_args', $args, $venue_id );

	$iframe = sprintf(
		'<iframe src="%s" width="%s" height="%s" frameBorder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>',
		esc_url( $args['embed_url'] ),
		esc_attr( $args['width'] ),
		esc_attr( $args['height'] )
	);

	$link = sprintf(
		'<a href="%s" target="_blank">%s</a>',
		esc_url( $args['link_url'] ),
		$args['link_text']
	);

	$output = sprintf( $args['format'], $iframe, $link );

	return apply_filters( 'bandstand_google_map_embed', $output, $args, $venue_id );
}

/**
 * Retrieve the static Google map URL for an address/venue.
 *
 * @since 1.0.0
 *
 * @link https://developers.google.com/maps/documentation/staticmaps/?csw=1
 *
 * @param array $args {
 *     Optional. Array of arguments.
 *
 *     @type string $address The address to send to Google.
 *     @type string $width   Width of the image. Defaults to 640.
 *     @type string $height  Height of the image. Defaults to 300.
 * }
 * @param  int   $venue_id Optional. Venue ID.
 * @return string
 */
function get_bandstand_google_static_map_url( $args = array(), $venue_id = 0 ) {
	$args = wp_parse_args( $args, array(
		'address'   => '',
		'width'     => 640,
		'height'    => 300,
	) );

	// Get the current post and determine if it's a gig with a venue.
	if ( empty( $args['address'] ) && ( $gig = get_bandstand_gig() ) ) {
		if ( 'bandstand_gig' === get_post_type( $gig ) && $gig->has_venue() ) {
			$venue_id = $gig->get_venue()->ID;
		}
	}

	// Retrieve the address for the venue.
	if ( $venue_id ) {
		$venue = get_bandstand_venue( $venue_id );

		$args['address'] = get_bandstand_venue_address( $venue->ID );
		$args['address'] = empty( $args['address'] ) ? $venue->get_name() : $venue->get_name() . ', ' . $args['address'];
	}

	$image_url = add_query_arg(
		array(
			'center'  => rawurlencode( $args['address'] ),
			'size'    => $args['width'] . 'x' . $args['height'],
			'scale'   => 2,
			'format'  => 'jpg',
			'sensor'  => 'false',
			'markers' => 'size:small|color:0xff0000|' . rawurlencode( $args['address'] ),
			'key'     => bandstand()->modules['gigs']->get_google_maps_api_key(),
		),
		'//maps.googleapis.com/maps/api/staticmap'
	);

	$image_url = apply_filters( 'bandstand_google_static_map_url', $image_url, $args, $venue_id );

	// @link https://developers.google.com/maps/documentation/staticmaps/?csw=1#StyledMaps
	$map_styles = apply_filters( 'bandstand_google_static_map_styles', array() );
	if ( ! empty( $map_styles ) ) {
		foreach ( $map_styles as $styles ) {
			$image_url .= '&style=' . bandstand_build_query( $styles );
		}
	}

	return $image_url;
}
