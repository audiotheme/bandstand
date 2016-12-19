<?php
/**
 * The template to display a record on archives.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$record = get_bandstand_record();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bandstand-block' ); ?> itemscope itemtype="http://schema.org/MusicAlbum">

	<figure class="bandstand-block-media">
		<a href="<?php the_permalink(); ?>" class="bandstand-thumbnail">
			<?php the_post_thumbnail( 'bandstand-thumbnail', array( 'itemprop' => 'image' ) ); ?>
		</a>
	</figure>

	<?php the_title( '<h2 class="bandstand-block-title entry-title" itemprop="name"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>

	<?php if ( $record->has_artist() ) : ?>
		<p class="bandstand-record-meta entry-meta">
			<span class="bandstand-record-meta-artist" itemprop="byArtist"><?php echo esc_html( $record->get_artist() ); ?></span>
		</p>
	<?php endif; ?>

	<?php if ( $record->has_release_date() ) : ?>
		<meta itemprop="dateCreated" content="<?php echo esc_html( $record->get_release_date( 'c' ) ); ?>">
	<?php endif; ?>

</article>
