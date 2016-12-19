<?php
/**
 * The template used for displaying metadata on single record pages.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$record = get_bandstand_record();
?>

<?php if ( $record->get_track_count()  ) : ?>

	<div class="bandstand-tracklist-section">
		<h2 class="bandstand-tracklist-title bandstand-label"><?php esc_html_e( 'Tracklist', 'bandstand' ); ?></h2>
		<ol class="bandstand-tracklist">

			<?php foreach ( $record->get_tracks() as $track ) : ?>

				<li id="track-<?php echo absint( $track->ID ); ?>" class="bandstand-track" itemprop="track" itemscope itemtype="http://schema.org/MusicRecording">
					<span class="bandstand-track-info bandstand-track-cell">
						<a href="<?php echo esc_url( get_permalink( $track->ID ) ); ?>" itemprop="url" class="bandstand-track-title"><span itemprop="name"><?php echo get_the_title( $track->ID ); ?></span></a>

						<span class="bandstand-track-meta">
							<?php if ( $track->is_downloadable() ) : ?>
								<a href="<?php echo esc_url( $track->get_download_url() ); ?>" class="bandstand-track-download-link"><?php esc_html_e( 'Download', 'bandstand' ); ?></a>
							<?php endif; ?>

							<span class="bandstand-track-duration"><?php echo esc_html( $track->get_duration() ); ?></span>
						</span>
					</span>
				</li>

			<?php endforeach; ?>
		</ol>
	</div><!-- /.tracklist-section -->

<?php endif; ?>
