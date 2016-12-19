/* jshint browserify: true */

'use strict';

var Track,
	BaseModel = require( 'wp' ).api.WPApiBaseModel,
	CueboneTrack = require( 'cuebone/src/models/track' ),
	settings = require( 'bandstand' ).settings();

Track = BaseModel.extend({
	methods: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ],

	urlRoot: function() {
		return settings.restUrl + 'bandstand/v1/tracks';
	},

	set: function( key, value, options ) {
		// Coerce track_number to a string.
		if ( 'track_number' === key ) {
			value = String( value );
		} else if ( 'object' === typeof key && 'track_number' in key ) {
			key.track_number = String( key.track_number );
		}

		BaseModel.prototype.set.call( this, key, value, options );
	},

	defaults: {
		id: null,
		artist: '',
		artwork_url: '',
		download_url: '',
		duration: '',
		menu_order: 0,
		purchase_url: '',
		record_id: 0,
		status: 'draft',
		stream_url: '',
		title: '',
		track_number: ''
	}
}).extend( CueboneTrack.prototype );

module.exports = Track;
