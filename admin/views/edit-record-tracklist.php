<?php
/**
 * View the record tracklist.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<div class="bandstand-panel bandstand-panel--tracklist">
	<div class="bandstand-panel-header">
		<h2 class="bandstand-panel-title"><?php esc_html_e( 'Tracklist', 'bandstand' ); ?></h2>
	</div>
	<div class="bandstand-panel-body">
		<div id="bandstand-tracklist" class="bandstand-tracklist"></div>
	</div>
	<div class="bandstand-panel-footer">
		<button type="button" class="button js-open-tracklist-manager"><?php esc_html_e( 'Manage Tracklist', 'bandstand' ); ?></button>
	</div>
</div>
