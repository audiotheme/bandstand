<?php
/**
 * The template to display the loop on the single video page.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

do_action( 'bandstand_before_loop' );
?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php get_bandstand_template_part( 'video/content', 'single' ); ?>

<?php endwhile; ?>

<?php
do_action( 'bandstand_after_loop' );
