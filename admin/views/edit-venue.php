<?php
/**
 * View to display the main venue fields.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<div id="bandstand-venue-details-panel" class="bandstand-panel">
	<div class="bandstand-panel-header">
		<h3 class="bandstand-panel-title" style="padding: 0"><?php esc_html_e( 'Details', 'bandstand' ); ?></h3>
	</div>
	<div class="bandstand-panel-body">
		<table class="form-table" >
			<tr>
				<th><label for="venue-address"><?php esc_html_e( 'Address', 'bandstand' ) ?></label></th>
				<td><textarea name="bandstand_venue[address]" id="venue-address" cols="30" rows="2"><?php echo esc_textarea( $venue->address ); ?></textarea></td>
			</tr>
			<tr>
				<th><label for="venue-city"><?php esc_html_e( 'City', 'bandstand' ) ?></label></th>
				<td><input type="text" name="bandstand_venue[city]" id="venue-city" class="regular-text" value="<?php echo esc_attr( $venue->city ); ?>"></td>
			</tr>
			<tr>
				<th><label for="venue-region"><?php esc_html_e( 'State', 'bandstand' ) ?></label></th>
				<td><input type="text" name="bandstand_venue[region]" id="venue-region" class="regular-text" value="<?php echo esc_attr( $venue->region ); ?>"></td>
			</tr>
			<tr>
				<th><label for="venue-postal-code"><?php esc_html_e( 'Postal Code', 'bandstand' ) ?></label></th>
				<td><input type="text" name="bandstand_venue[postal_code]" id="venue-postal-code" class="regular-text" value="<?php echo esc_attr( $venue->postal_code ); ?>"></td>
			</tr>
			<tr>
				<th><label for="venue-country"><?php esc_html_e( 'Country', 'bandstand' ) ?></label></th>
				<td><input type="text" name="bandstand_venue[country]" id="venue-country" class="regular-text" value="<?php echo esc_attr( $venue->country ); ?>"></td>
			</tr>
			<tr>
				<th><label for="venue-timezone-id"><?php esc_html_e( 'Time zone', 'bandstand' ) ?></label></th>
				<td>
					<select id="venue-timezone-id" name="bandstand_venue[timezone_id]">
						<?php echo bandstand_timezone_choice( $venue->timezone_id ); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="venue-phone"><?php esc_html_e( 'Phone', 'bandstand' ) ?></label></th>
				<td><input type="text" name="bandstand_venue[phone]" id="venue-phone" class="regular-text" value="<?php echo esc_attr( $venue->phone ); ?>"></td>
			</tr>
			<tr>
				<th><label for="venue-website-url"><?php esc_html_e( 'Website URL', 'bandstand' ) ?></label></th>
				<td><input type="text" name="bandstand_venue[website_url]" id="venue-website-url" class="regular-text" value="<?php echo esc_url( $venue->website_url ); ?>"></td>
			</tr>
		</table>
	</div>
</div>
