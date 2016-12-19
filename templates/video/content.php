<?php
/**
 * The template to display a video.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$video = get_bandstand_video();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bandstand-video' ); ?> role="article" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">

	<header class="bandstand-video-header entry-header">
		<?php the_title( '<h1 class="bandstand-video-title entry-title" itemprop="name">', '</h1>' ); ?>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<meta itemprop="thumbnailUrl" content="<?php the_post_thumbnail_url( 'full' ); ?>">
	<?php endif; ?>

	<?php if ( $video->has_url() ) : ?>
		<meta itemprop="embedUrl" content="<?php echo esc_url( $video->get_url() ); ?>">

		<figure class="bandstand-embed entry-video">
			<?php the_bandstand_video_html(); ?>
		</figure>
	<?php endif; ?>

	<div class="bandstand-content entry-content" itemprop="description">
		<?php the_content( '' ); ?>
	</div>

	<?php if ( $term_list = get_the_term_list( get_the_ID(), 'bandstand_video_category', '', ' ' ) ) : ?>
		<dl class="bandstand-term-list">
			<dt class="bandstand-term-list-label"><?php esc_html_e( 'Categories', 'bandstand' ); ?></dt>
			<dd class="bandstand-term-list-items"><?php echo $term_list; ?></dd>
		</dl>
	<?php endif; ?>

</article>
