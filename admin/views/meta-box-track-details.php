<?php
/**
 * View for the track details meta box.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<p class="bandstand-field">
	<label for="track-artist"><?php esc_html_e( 'Artist:', 'bandstand' ) ?></label>
	<input type="text" name="bandstand_track[artist]" id="track-artist" value="<?php echo esc_attr( $post->bandstand_artist ); ?>" class="widefat">
</p>

<p class="bandstand-field">
	<label for="track-duration"><?php esc_html_e( 'Duration:', 'bandstand' ) ?></label>
	<input type="text" name="bandstand_track[duration]" id="track-duration" value="<?php echo esc_attr( $post->bandstand_duration ); ?>" placeholder="00:00" class="widefat">
</p>

<p class="bandstand-field bandstand-media-control bandstand-field-upload"
	data-title="<?php esc_attr_e( 'Choose an MP3', 'bandstand' ); ?>"
	data-update-text="<?php esc_attr_e( 'Use MP3', 'bandstand' ); ?>"
	data-target="#track-stream-url"
	data-return-property="url"
	data-file-type="audio">
	<label for="track-stream-url"><?php esc_html_e( 'Stream URL:', 'bandstand' ) ?></label>
	<input type="url" name="bandstand_track[stream_url]" id="track-stream-url" value="<?php echo esc_attr( $post->bandstand_stream_url ); ?>" class="widefat">

	<button class="button bandstand-media-control-choose" style="float: right"><?php esc_html_e( 'Upload MP3', 'bandstand' ); ?></button>
</p>

<p class="bandstand-field bandstand-media-control bandstand-field-upload"
	data-title="<?php esc_attr_e( 'Choose a File', 'bandstand' ); ?>"
	data-update-text="<?php esc_attr_e( 'Use File', 'bandstand' ); ?>"
	data-target="#track-download-url"
	data-return-property="url"
	data-file-type="audio">
	<label for="track-download-url"><?php esc_html_e( 'Download URL:', 'bandstand' ) ?></label>
	<input type="url" name="bandstand_track[download_url]" id="track-download-url" value="<?php echo esc_attr( $post->bandstand_download_url ); ?>" class="widefat">

	<button class="button bandstand-media-control-choose" style="float: right"><?php esc_html_e( 'Upload File', 'bandstand' ); ?></button>
</p>

<p class="bandstand-field">
	<label for="track-purchase-url"><?php esc_html_e( 'Purchase URL:', 'bandstand' ) ?></label>
	<input type="url" name="bandstand_track[purchase_url]" id="track-purchase-url" value="<?php echo esc_url( $post->bandstand_purchase_url ); ?>" class="widefat">
</p>

<?php
if ( empty( $post->post_parent ) || ! get_post( $post->post_parent ) ) {
	$records = get_posts( 'post_type=bandstand_record&orderby=title&order=asc&posts_per_page=-1' );
	if ( $records ) {
		echo '<p class="bandstand-field">';
			echo '<label for="post-parent">' . esc_html__( 'Record:', 'bandstand' ) . '</label>';
			echo '<select name="post_parent" id="post-parent" class="widefat">';
				echo '<option value=""></option>';

				foreach ( $records as $record ) {
					printf(
						'<option value="%s">%s</option>',
						absint( $record->ID ),
						esc_html( $record->post_title )
					);
				}

			echo '</select>';
			echo '<span class="description">' . esc_html__( 'Associate this track with a record.', 'bandstand' ) . '</span>';
		echo '</p>';
	}
}
