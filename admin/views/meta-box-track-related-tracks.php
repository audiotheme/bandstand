<?php
/**
 * View for the track details meta box.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$tracks = $record->get_tracks();

if ( ! empty( $tracks ) ) :
?>

	<ol class="bandstand-tracklist">
		<?php
		foreach ( $tracks as $track ) {
			echo '<li>';
				if ( $track->ID === $post->ID ) {
					echo esc_html( get_the_title( $track->ID ) );
				} else {
					printf(
						'<a href="%s">%s</a>',
						esc_url( get_edit_post_link( $track->ID ) ),
						esc_html( get_the_title( $track->ID ) )
					);
				}
			echo '</li>';
		}
		?>
	</ol>

<?php
endif;
