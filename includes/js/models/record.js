/* jshint browserify: true */

'use strict';

var Record,
	Backbone = require( 'backbone' ),
	settings = require( 'bandstand' ).settings();

Record = Backbone.Model.extend({
	urlRoot: function() {
		return settings.restUrl + 'bandstand/v1/records';
	},

	defaults: {
		id: null,
		artist: '',
		catalog_number: '',
		label: '',
		release_date: '',
		title: '',
		tracks: [],
		track_count: 0
	}
});

module.exports = Record;
