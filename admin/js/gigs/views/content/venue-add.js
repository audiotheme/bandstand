/* jshint browserify: true */

var VenueAddContent,
	VenueAddForm = require( '../venue/add-form' ),
	wp = require( 'wp' );

VenueAddContent = wp.media.View.extend({
	className: 'bandstand-venue-frame-content bandstand-venue-frame-content--add',

	initialize: function( options ) {
		this.model = options.model;
	},

	render: function() {
		this.views.add([
			new VenueAddForm({
				model: this.model
			})
		]);
		return this;
	}
});

module.exports = VenueAddContent;
