<?php
/**
 * Gig Underscore.js templates.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<script type="text/html" id="tmpl-bandstand-gig-venue-details">
	<h5 class="venue-name">{{ data.name }}</h5>

	<# if ( ! data.isAddressEmpty() ) { #>
		<p class="venue-address">
			<# if ( data.address ) { #>
				{{ data.address }}<br>
			<# } #>
			{{ data.formatCityRegionPostalCode() }}<# if ( '' !== data.formatCityRegionPostalCode() ) { #>,<# } #>
			{{ data.country }}
		</p>
	<# } #>

	<# if ( data.phone ) { #>
		<p class="venue-phone">{{ data.phone }}</p>
	<# } #>

	<# if ( data.url ) { #>
		<p class="venue-url">{{ data.url }}</p>
	<# } #>
</script>
