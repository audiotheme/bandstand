/* jshint browserify: true */

var $ = require( 'jquery' ),
	placeAutocomplete = require( './gigs/utils/place-autocomplete' );

placeAutocomplete({
	input: $( '#title' )[0],
	fields: {
		name: $( '#title' ),
		address: $( '#venue-address' ),
		city: $( '#venue-city' ),
		region: $( '#venue-region' ),
		postalCode: $( '#venue-postal-code' ),
		country: $( '#venue-country' ),
		timeZone: $( '#venue-timezone-id' ),
		phone: $( '#venue-phone' ),
		website_url: $( '#venue-website-url' )
	},
	type: 'establishment'
});

placeAutocomplete({
	input: $( '#venue-city' )[0],
	fields: {
		city: $( '#venue-city' ),
		region: $( '#venue-region' ),
		country: $( '#venue-country' ),
		timeZone: $( '#venue-timezone-id' )
	},
	type: '(cities)'
});
