<?php
/**
 * View to display gig date, time, venue and notes fields.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<?php
/*
if ( empty( $timezone_id ) && 'auto-draft' !== get_post_status() ) : ?>
	<div class="">
		This event doesn't have a time zone. Choose one now or <a href="">read more about the importance of time zones</a>:<br>

		<select name="bandstand_gig[timezone_id]" id="gig-venue-timezone" data-setting="timezone">
			<?php echo bandstand_timezone_choice( $timezone_id ); ?>
		</select>
	</div>
<?php endif;
*/
?>

<div class="bandstand-gig-editor">

	<div class="bandstand-gig-editor-primary">
		<div class="bandstand-gig-date-picker bandstand-gig-date-picker-start">
			<div id="bandstand-gig-start-date-picker"></div>
			<div class="bandstand-gig-date-picker-footer">
				<input type="text" name="bandstand_gig[start_date]" id="gig-date" value="<?php echo esc_attr( $gig->format_start_date( 'Y-m-d' ) ); ?>" placeholder="YYYY-MM-DD" autocomplete="off">
			</div>
		</div>
	</div>

	<div class="bandstand-gig-editor-secondary">

		<div class="bandstand-panel">
			<div class="bandstand-panel-header">
				<h4 class="bandstand-panel-title"><?php esc_html_e( 'Time', 'bandstand' ); ?></h4>
			</div>
			<div class="bandstand-panel-body">
				<div class="bandstand-gig-time-picker bandstand-input-group">
					<input type="text" name="bandstand_gig[start_time]" id="gig-time" value="<?php echo esc_attr( $gig_time ); ?>" placeholder="HH:MM" class="bandstand-input-group-field ui-autocomplete-input">
					<label for="gig-time" id="gig-time-select" class="bandstand-input-group-trigger dashicons dashicons-clock"></label>
				</div>
			</div>
		</div>

		<div id="bandstand-gig-venue-meta-box" class="bandstand-panel">
			<div class="bandstand-panel-header">
				<h4 class="bandstand-panel-title"><?php esc_html_e( 'Venue', 'bandstand' ); ?></h4>
				<input type="hidden" name="bandstand_gig[venue_id]" id="gig-venue-id" value="<?php echo absint( $venue_id ); ?>">
			</div>
			<div class="bandstand-panel-body">

			</div>
		</div>

	</div>

	<div id="bandstand-gig-note-meta-box" class="bandstand-panel">
		<div class="bandstand-panel-header">
			<h4 class="bandstand-panel-title"><?php esc_html_e( 'Note', 'bandstand' ) ?></h4>
		</div>
		<div class="bandstand-panel-body">
			<textarea name="excerpt" id="excerpt" cols="76" rows="3"><?php echo $post->post_excerpt; ?></textarea>
			<span class="description"><?php esc_html_e( 'A description of the gig to display within the list of gigs. Who is the opening act, special guests, etc? Keep it short.', 'bandstand' ); ?></span>
		</div>
	</div>

</div>
