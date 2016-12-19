<?php
/**
 * The template for displaying a video card.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$video = get_bandstand_video();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bandstand-block' ); ?>>
	<figure class="bandstand-block-media">
		<a href="<?php the_permalink(); ?>" class="bandstand-thumbnail bandstand-thumbnail--16x9">
			<?php the_post_thumbnail( 'bandstand-thumbnail-16x9' ); ?>
			<span class="bandstand-thumbnail-action"><?php esc_html_e( 'Watch Now', 'bandstand' ); ?></span>
		</a>

		<?php if ( $video->has_duration() ) : ?>
			<dl class="bandstand-block-media-meta">
				<dt><?php esc_html_e( 'Duration', 'bandstand' ); ?></dt>
				<dd><?php echo esc_html( $video->get_duration() ); ?></dd>
			</dl>
		<?php endif; ?>
	</figure>

	<div class="bandstand-block-content">
		<?php the_title( '<h2 class="bandstand-block-title entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>

		<div class="bandstand-block-description">
			<?php if ( has_excerpt() ) : ?>
				<?php the_excerpt(); ?>
			<?php endif; ?>
		</div>
	</div>
</article>
