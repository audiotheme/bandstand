/* jshint browserify: true */

var VenueAddForm,
	$ = require( 'jquery' ),
	wp = require( 'wp' ),
	placeAutocomplete = require( '../../utils/place-autocomplete' );

/**
 *
 *
 * @todo Search for timezone based on the city.
 * @todo Display an error if the timezone isn't set.
 */
VenueAddForm = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-venue-edit-form',
	template: wp.template( 'bandstand-venue-edit-form' ),

	events: {
		'change [data-setting]': 'updateAttribute'
	},

	initialize: function( options ) {
		this.model = options.model;
	},

	render: function() {
		this.$el.html( this.template( this.model.toJSON() ) );

		placeAutocomplete({
			input: this.$( '[data-setting="name"]' )[0],
			fields: {
				name: this.$( '[data-setting="name"]' ),
				address: this.$( '[data-setting="address"]' ),
				city: this.$( '[data-setting="city"]' ),
				region: this.$( '[data-setting="region"]' ),
				postalCode: this.$( '[data-setting="postal_code"]' ),
				country: this.$( '[data-setting="country"]' ),
				timeZone: this.$( '[data-setting="timezone_string"]' ),
				phone: this.$( '[data-setting="phone"]' ),
				website_url: this.$( '[data-setting="website_url"]' )
			},
			type: 'establishment'
		});

		placeAutocomplete({
			input: this.$( '[data-setting="city"]' )[0],
			fields: {
				city: this.$( '[data-setting="city"]' ),
				region: this.$( '[data-setting="region"]' ),
				country: this.$( '[data-setting="country"]' ),
				timeZone: this.$( '[data-setting="timezone_string"]' )
			},
			type: '(cities)'
		});

		return this;
	},

	/**
	 * Update a model attribute when a field is changed.
	 *
	 * Fields with a 'data-setting="{{key}}"' attribute whose value
	 * corresponds to a model attribute will be automatically synced.
	 *
	 * @param {Object} e Event object.
	 */
	updateAttribute: function( e ) {
		var attribute = $( e.target ).data( 'setting' ),
			value = e.target.value;

		if ( this.model.get( attribute ) !== value ) {
			this.model.set( attribute, value );
		}
	}
});

module.exports = VenueAddForm;
