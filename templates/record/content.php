<?php
/**
 * The template to display a single record.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$record = get_bandstand_record();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bandstand-record' ); ?> itemscope itemtype="http://schema.org/MusicAlbum" role="article">

	<header class="bandstand-record-header entry-header">
		<?php the_title( '<h1 class="bandstand-record-title entry-title" itemprop="name">', '</h1>' ); ?>

		<?php if ( $record->has_artist() ) : ?>
			<h2 class="bandstand-record-artist" itemprop="byArtist"><?php echo esc_html( $record->get_artist() ); ?></h2>
		<?php endif; ?>

		<?php get_bandstand_template_part( 'record/meta' ); ?>
		<?php get_bandstand_template_part( 'record/meta-links' ); ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="bandstand-record-artwork">
			<a href="<?php the_post_thumbnail_url(); ?>" itemprop="image">
				<?php the_post_thumbnail( 'record-thumbnail' ); ?>
			</a>
		</figure>
	<?php endif; ?>

	<?php get_bandstand_template_part( 'record/tracklist' ); ?>

	<div class="bandstand-content entry-content" itemprop="description">
		<?php the_content( '' ); ?>

		<?php
		$tracks = bandstand_rest_request( 'GET', '/bandstand/v1/tracks', array(
			'record' => get_the_ID(),
			'format' => 'cuebone',
		) );

		// echo '<pre>';
		// print_r( $tracks );
		// echo '</pre>';
		//
		// $data = sprintf( 'var _bandstandTracks = %s;', wp_json_encode( $tracks ) );
		// wp_add_inline_script( 'bandstand-tracklists', $data, 'before' );
		// wp_enqueue_script( 'bandstand-tracklists' );
		// enqueue_bandstand_tracklist();
		?>
	</div><!-- /.content -->

</article><!-- /.single-bandstand-record -->

<?php
function enqueue_bandstand_tracklist( $post = null ) {
	$post_id = get_post( $post )->ID;

	$tracks = bandstand_rest_request( 'GET', '/bandstand/v1/tracks', array(
		'record' => $post_id,
		'format' => 'cuebone',
	) );

	$data = sprintf( 'var _bandstandTracks = %s;', wp_json_encode( $tracks ) );
	wp_add_inline_script( 'bandstand-tracklists', $data, 'before' );
	wp_enqueue_script( 'bandstand-tracklists' );
}
