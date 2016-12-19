/* jshint browserify: true */

var Record,
	BaseCollection = require( 'wp' ).api.WPApiBaseCollection,
	settings = require( 'bandstand' ).settings();

Record = BaseCollection.extend({
	methods: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ],

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
