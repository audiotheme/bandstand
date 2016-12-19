<?php
/**
 * Dashboard Underscore.js templates.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<script type="text/html" id="tmpl-bandstand-module-modal-header">
	<button class="left dashicons dashicons-no js-previous"><span class="screen-reader-text"><?php esc_html_e( 'Show previous module', 'bandstand' ); ?></span></button>
	<button class="right dashicons dashicons-no js-next"><span class="screen-reader-text"><?php esc_html_e( 'Show next module', 'bandstand' ); ?></span></button>
	<button class="close dashicons dashicons-no js-close"><span class="screen-reader-text"><?php esc_html_e( 'Close overlay', 'bandstand' ); ?></span></button>
</script>

<script type="text/html" id="tmpl-bandstand-module-modal-content">
	<div class="bandstand-overlay-content-primary">
		<h1 class="bandstand-overlay-content-title">{{{ data.name }}}</h1>
		<div class="bandstand-overlay-content-body">{{{ data.overview }}}</div>
	</div>
	<div class="bandstand-overlay-content-secondary">
		{{{ data.media }}}
	</div>
</script>
