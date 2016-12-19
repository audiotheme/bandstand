/* jshint browserify: true */

var Venues,
	BaseCollection = require( 'wp' ).api.WPApiBaseCollection,
	settings = require( 'bandstand' ).settings(),
	Venue = require( '../models/venue' );

Venues = BaseCollection.extend({
	model: Venue,

	url: function() {
		return settings.restUrl + 'bandstand/v1/venues';
	},

	observe: function( collection ) {
		var self = this;

		collection.on( 'change', function( model ) {
			self.set( model, { add: false, remove: false });
		});
	}
});

module.exports = Venues;
