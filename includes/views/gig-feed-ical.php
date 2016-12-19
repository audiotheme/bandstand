<?php
/**
 * Gigs iCal feed template.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

header( 'Content-type: text/calendar' );
header( 'Content-Disposition: attachment; filename="bandstand-gigs.ics"' );
?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Bandstand <?php echo BANDSTAND_VERSION; ?>

<?php
foreach ( $wp_query->posts as $post ) {
	$gig = get_bandstand_gig( $post );

	echo "BEGIN:VEVENT\n";
	echo 'UID:' . get_the_guid( $post->ID ) . "\n";
	echo 'URL:' . get_permalink( $post->ID ) . "\n";

	$date = $gig->format_start_date( 'Ymd', '', array( 'utc' => true ) );
	$time = $gig->format_start_date( '', 'His', array( 'utc' => true ) );

	printf(
		"DTSTART%s%s%s\n",
		empty( $time ) ? ';VALUE=DATE:' : ';TZID=GMT:',
		$date,
		empty( $time ) ? '' : 'T' . $time
	);

	echo 'SUMMARY:' . escape_ical_text( $gig->post_title ) . "\n";

	if ( ! empty( $post->post_excerpt ) ) {
		echo 'DESCRIPTION:' . escape_ical_text( $post->post_excerpt ) . "\n";
	}

	if ( $gig->has_venue() ) {
		$location = get_bandstand_venue_location_ical( $gig->get_venue()->ID );
		echo empty( $location ) ? '' : 'LOCATION:' . $location . "\n";
	}

	echo "END:VEVENT\n";
}
?>
END:VCALENDAR
