<?php
/**
 * The template to display a singular track loop.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

do_action( 'bandstand_before_loop' );
?>

<div class="no-fouc" data-bandstand-media-classes="400,600">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_bandstand_template_part( 'track/content', 'single' ); ?>

	<?php endwhile; ?>

</div>

<?php
do_action( 'bandstand_after_loop' );
