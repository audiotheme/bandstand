/* jshint browserify: true */

var VenueEditForm,
	$ = require( 'jquery' ),
	wp = require( 'wp' ),
	placeAutocomplete = require( '../../utils/place-autocomplete' );

VenueEditForm = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-venue-edit-form',
	template: wp.template( 'bandstand-venue-edit-form' ),

	events: {
		'change [data-setting]': 'updateAttribute'
	},

	initialize: function( options ) {
		this.model = options.model;
		this.$spinner = $( '<span class="spinner"></span>' );
	},

	render: function() {
		var timezoneId = this.model.get( 'timezone_id' );

		this.$el.html( this.template( this.model.toJSON() ) );

		if ( timezoneId ) {
			this.$el.find( '#venue-timezone-id' ).find( 'option[value="' + timezoneId + '"]' ).prop( 'selected', true );
		}

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
		var $target = $( e.target ),
			attribute = $target.data( 'setting' ),
			value = e.target.value,
			$spinner = this.$spinner;

		if ( this.model.get( attribute ) !== value ) {
			$spinner.insertAfter( $target ).addClass( 'is-active' );

			this.model.set( attribute, value ).save().always(function() {
				$spinner.removeClass( 'is-active' );
			});
		}
	}
});

module.exports = VenueEditForm;
