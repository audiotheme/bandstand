<?php
/**
 * The template to display a single track.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$track = get_bandstand_track();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bandstand-record-single bandstand-track-single' ); ?> itemscope itemtype="http://schema.org/MusicRecording" role="article">

	<header class="bandstand-record-header entry-header">
		<?php the_title( '<h1 class="bandstand-record-title entry-title" itemprop="name">', '</h1>' ); ?>

		<?php the_bandstand_record_artist( '<h2 class="bandstand-record-artist" itemprop="byArtist">', '</h2>' ); ?>

		<?php
		the_bandstand_record_title(
			'<h3 class="bandstand-record-subtitle"><a href="' . esc_url( get_permalink( $track->get_record()->ID ) ) . '"><em itemprop="inAlbum">',
			'</em></a></h3>'
		);
		?>

		<?php
		if ( $track->is_downloadable() || $track->has_purchase_url() ) :
			?>
			<div class="bandstand-record-links bandstand-track-links">
				<ul class="bandstand-record-links-list">
					<?php if ( $track->is_downloadable() ) : ?>
						<li class="bandstand-record-links-item">
							<a href="<?php echo esc_url( $track->get_download_url() ); ?>" class="bandstand-record-link" itemprop="url" target="_blank"><?php esc_html_e( 'Download', 'bandstand' ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( $track->has_purchase_url() ) : ?>
						<li class="bandstand-record-links-item">
							<a href="<?php echo esc_url( $track->get_purchase_url() ); ?>" class="bandstand-record-link" itemprop="url" target="_blank"><?php esc_html_e( 'Purchase', 'bandstand' ); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</div><!-- /.record-links -->
		<?php endif; ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="bandstand-record-artwork">
			<a href="<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>" itemprop="image">
				<?php the_post_thumbnail( 'record-thumbnail' ); ?>
			</a>
		</figure>
	<?php endif; ?>

	<div class="bandstand-tracklist-section">
		<ol class="bandstand-tracklist bandstand-tracklist-single">

			<li id="track-<?php the_ID(); ?>" class="bandstand-track">
				<span class="bandstand-track-info bandstand-track-cell">
					<span class="bandstand-track-title"><?php the_title(); ?></span>

					<span class="bandstand-track-meta">
						<span class="bandstand-track-duration"><?php echo esc_html( $track->get_duration() ); ?></span>
					</span>
				</span>
			</li>

		</ol>
	</div><!-- /.tracklist-section -->

	<div class="bandstand-content entry-content" itemprop="description">
		<?php the_content( '' ); ?>
	</div><!-- /.content -->

</article><!-- /.single-bandstand-record -->
