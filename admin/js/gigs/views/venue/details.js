/* jshint browserify: true */

var VenueDetails,
	_ = require( 'underscore' ),
	templateHelpers = require( '../../utils/template-helpers' ),
	wp = require( 'wp' );

VenueDetails = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-venue-details',
	template: wp.template( 'bandstand-venue-details' ),

	initialize: function( options ) {
		this.model = options.model;
	},

	render: function() {
		var data = _.extend( this.model.toJSON(), templateHelpers );
		this.$el.html( this.template( data ) );
		return this;
	}
});

module.exports = VenueDetails;
