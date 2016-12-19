<?php
/**
 * Venue Underscore.js templates.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<script type="text/html" id="tmpl-bandstand-venues-search-field">
	<input type="search" placeholder="<?php esc_html_e( 'Search Venues', 'bandstand' ); ?>">
</script>

<script type="text/html" id="tmpl-bandstand-venue-panel-title">
	<h2>{{ data.name }}</h2>
	<button class="button"><?php esc_html_e( 'Edit', 'bandstand' ); ?></button>
</script>

<script type="text/html" id="tmpl-bandstand-venue-details">
	<table>
		<# if ( ! data.isAddressEmpty() ) { #>
			<tr>
				<th><?php esc_html_e( 'Address:', 'bandstand' ) ?></th>
				<td>
					<# if ( data.address ) { #>
						{{ data.address }}<br>
					<# } #>
					{{ data.formatCityRegionPostalCode() }}
					<# if ( '' !== data.formatCityRegionPostalCode() ) { #>
						<br>
					<# } #>
					{{ data.country }}
				</td>
			</tr>
		<# } #>

		<# if ( data.phone ) { #>
			<tr>
				<th><?php esc_html_e( 'Phone:', 'bandstand' ); ?></th>
				<td>{{ data.phone }}</td>
			</tr>
		<# } #>

		<# if ( data.website_url ) { #>
			<tr>
				<th><?php esc_html_e( 'Website URL:', 'bandstand' ); ?></th>
				<td>{{ data.website_url }}</td>
			</tr>
		<# } #>
	</table>
</script>

<script type="text/html" id="tmpl-bandstand-venue-edit-form">
	<table>
		<tr>
			<th><label for="venue-name"><?php esc_html_e( 'Name', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_venue[name]" id="venue-name" class="regular-text" value="{{ data.name }}" data-setting="name" placeholder="">
			</td>
		</tr>
		<tr>
			<th><label for="venue-address"><?php esc_html_e( 'Address', 'bandstand' ) ?></label></th>
			<td>
				<textarea name="bandstand_venue[address]" id="venue-address" class="regular-text" cols="30" rows="2" data-setting="address">{{ data.address }}</textarea>
			</td>
		</tr>
		<tr>
			<th><label for="venue-city"><?php esc_html_e( 'City', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_venue[city]" id="venue-city" class="regular-text" value="{{ data.city }}" data-setting="city" placeholder="">
			</td>
		</tr>
		<tr>
			<th><label for="venue-region"><?php esc_html_e( 'State', 'bandstand' ) ?></label></th>
			<td>
				 <input type="text" name="bandstand_venue[region]" id="venue-region" class="regular-text" value="{{ data.region }}" data-setting="region">
			</td>
		</tr>
		<tr>
			<th><label for="venue-postal-code"><?php esc_html_e( 'Postal Code', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_venue[postal_code]" id="venue-postal-code" class="regular-text" value="{{ data.postal_code }}" data-setting="postal_code">
			</td>
		</tr>
		<tr>
			<th><label for="venue-country"><?php esc_html_e( 'Country', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_venue[country]" id="venue-country" class="regular-text" value="{{ data.country }}" data-setting="country">
			</td>
		</tr>
		<tr>
			<th><label for="venue-timezone-id"><?php esc_html_e( 'Time zone', 'bandstand' ) ?></label></th>
			<td>
				<select id="venue-timezone-id" name="bandstand_venue[timezone_id]" data-setting="timezone_id">
					<?php echo bandstand_timezone_choice(); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="venue-phone"><?php esc_html_e( 'Phone', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_venue[phone]" id="venue-phone" class="regular-text" value="{{ data.phone }}" data-setting="phone">
			</td>
		</tr>
		<tr>
			<th><label for="venue-website-url"><?php esc_html_e( 'Website URL', 'bandstand' ) ?></label></th>
			<td>
				<input type="text" name="bandstand_venue[website_url]" id="venue-website-url" class="regular-text" value="{{ data.website_url }}" data-setting="website_url">
			</td>
		</tr>
	</table>
</script>
