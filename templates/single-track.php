<?php
/**
 * The template for displaying a single track.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

get_header();
?>

<?php do_action( 'bandstand_before_content' ); ?>

<?php get_bandstand_template_part( 'track/loop', 'single' ); ?>

<?php do_action( 'bandstand_after_content' ); ?>

<?php
get_footer();
