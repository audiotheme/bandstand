<?php
/**
 * Record Underscore.js templates.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<script type="text/html" id="tmpl-bandstand-track-edit-form">
	<table>
		<tr>
			<th><label for="track-number"><?php esc_html_e( 'Number', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_track[track_number]" id="track-number" class="regular-text" value="{{ data.track_number }}" data-setting="track_number" style="width: 5em">
			</td>
		</tr>
		<tr>
			<th><label for="track-title"><?php esc_html_e( 'Title', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_track[title]" id="track-title" class="regular-text" value="{{ data.displayRawTitle() }}" data-setting="title">
			</td>
		</tr>
		<tr>
			<th><label for="track-artist"><?php esc_html_e( 'Artist', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_track[artist]" id="track-artist" class="regular-text" value="{{ data.artist }}" data-setting="artist">
			</td>
		</tr>
		<tr>
			<th><label for="track-duration"><?php esc_html_e( 'Duration', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_track[duration]" id="track-duration" class="regular-text" value="{{ data.duration }}" data-setting="duration" style="width: 5em">
			</td>
		</tr>
	</table>
</script>

<script type="text/html" id="tmpl-bandstand-track-meta">
	<h2><?php esc_html_e( 'Track Meta', 'bandstand' ); ?></h2>

	<p>
		<label for="track-stream-url"><?php esc_html_e( 'Stream URL', 'bandstand' ) ?></label>
		<span class="bandstand-input-group">
			<input type="text" name="bandstand_track[stream_url]" id="track-stream-url" class="widefat bandstand-input-group-field" value="{{ data.stream_url }}" data-setting="stream_url">
			<span class="bandstand-input-group-button"><button class="button button-secondary dashicons dashicons-format-audio js-select-attachment"></button></span>
		</span>
	</p>
	<p>
		<label for="track-download-url"><?php esc_html_e( 'Download URL', 'bandstand' ) ?></label>
		<span class="bandstand-input-group">
			<input type="text" name="bandstand_track[download_url]" id="track-download-url" class="widefat bandstand-input-group-field" value="{{ data.download_url }}" data-setting="download_url">
			<span class="bandstand-input-group-button"><button class="button button-secondary dashicons dashicons-format-audio js-select-attachment"></button></span>
		</span>
	</p>
	<p>
		<label for="track-purchase-url"><?php esc_html_e( 'Purchase URL', 'bandstand' ) ?></label></th>
		<input type="text" name="bandstand_track[purchase_url]" id="track-purchase-url" class="widefat" value="{{ data.purchase_url }}" data-setting="purchase_url">
	</p>
	<hr>
	<p>
		<button type="button" class="button-link bandstand-delete-track js-delete-track"><?php esc_html_e( 'Delete', 'bandstand' ); ?></button>
	</p>
</script>

<script type="text/html" id="tmpl-bandstand-record-finder-search-group">
	<div class="bandstand-record-finder-search">
		<div class="bandstand-input-group">
			<input type="search" id="bandstand-record-finder-search-field" class="bandstand-input-group-field widefat">
			<span class="bandstand-input-group-button">
				<button type="button" id="bandstand-record-finder-button" class="button"><?php esc_html_e( 'Search', 'bandstand' ); ?></button>
			</span>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-bandstand-record-finder-services-panel">
	<div class="bandstand-record-finder-service-dropdown">
		<select>
			<# _.each( data.services, function( service ) { #>
				<option value="{{ service.id }}"<# if ( service.id === data.selected ) { #> selected="selected"<# } #>>{{ service.name }}</option>
			<# } ); #>
		</select>
	</div>
</script>

<script type="text/html" id="tmpl-bandstand-record-finder-result">
	<# if ( data.artworkUrl ) { #>
		<span class="bandstand-record-finder-result-artwork">
			<img src="{{ data.artworkUrl }}">
		</span>
	<# } #>

	<span class="bandstand-record-finder-result-details">
		<a href="{{ data.externalUrl }}" target="blank"><strong>
			{{ data.title }}
			<# if ( data.artist ) { #>- {{ data.artist }}<# } #>
		</strong></a>
		<# if ( data.releaseDate ) { #>
			<br>
			<?php esc_html_e( 'Released:', 'bandstand' ); ?> {{ data.releaseDate }}
		<# } #>
		<# if ( data.recordLabel ) { #>
			<br>
			<?php esc_html_e( 'Label:', 'bandstand' ); ?> {{ data.recordLabel }}
		<# } #>
		<# if ( data.trackCount ) { #>
			<br>
			<?php esc_html_e( 'Tracks:', 'bandstand' ); ?> {{ data.trackCount }}
		<# } #>
	</span>
</script>
