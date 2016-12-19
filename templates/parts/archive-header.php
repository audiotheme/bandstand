<?php
/**
 * The template to display the header for an archive.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<header class="bandstand-archive-header">
	<?php the_bandstand_archive_title( '<h1 class="bandstand-archive-title">', '</h1>' ); ?>

	<?php if ( ! get_query_var( 'paged' ) && ! get_query_var( 'page' ) ) : ?>
		<?php the_bandstand_archive_description( '<div class="bandstand-archive-intro">', '</div>' ); ?>
	<?php endif; ?>
</header>
