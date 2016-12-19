/* jshint browserify: true */

var RecordTracks,
	$ = require( 'jquery' ),
	BaseCollection = require( 'wp' ).api.WPApiBaseCollection,
	settings = require( 'bandstand' ).settings(),
	Track = require( 'models/track' );

RecordTracks = BaseCollection.extend({
	model: Track,

	url: function() {
		return settings.restUrl + 'bandstand/v1/tracks';
	},

	initialize: function( models, options ) {
		options = options || {};
		this.recordId = options.recordId || null;
		BaseCollection.prototype.initialize.apply( this, arguments );
	},

	comparator: function( model ) {
		return parseInt( model.get( 'menu_order' ), 10 );
	},

	save: function() {
		var saved = [];

		this.each(function( model ) {
			saved.push( model.save() );
		});

		return $.when.apply( $, saved );
	}
});

module.exports = RecordTracks;
