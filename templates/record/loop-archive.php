<?php
/**
 * The template to display a record archive loop.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

do_action( 'bandstand_before_loop' );
?>

<div <?php bandstand_posts_container_class( 'bandstand-records bandstand-block-grid bandstand-block-grid--gutters no-fouc' ); ?> data-bandstand-media-classes="400,600">

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<div class="bandstand-block-grid-item">

				<?php get_bandstand_template_part( 'record/content', 'archive' ); ?>

			</div>

		<?php endwhile; ?>
	<?php endif; ?>

</div>

<?php do_action( 'bandstand_after_loop' ); ?>

<?php
the_posts_navigation( array(
	'prev_text' => esc_html__( 'Next', 'bandstand' ),
	'next_text' => esc_html__( 'Previous', 'bandstand' ),
) );
