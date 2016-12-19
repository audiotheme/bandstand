/* jshint browserify: true */

var Venue,
	BaseModel = require( 'wp' ).api.WPApiBaseModel,
	settings = require( 'bandstand' ).settings();

Venue = BaseModel.extend({
	methods: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ],

	urlRoot: function() {
		return settings.restUrl + 'bandstand/v1/venues';
	},

	defaults: {
		id: null,
		address: '',
		city: '',
		country: '',
		name: '',
		phone: '',
		postal_code: '',
		region: '',
		status: 'draft',
		timezone_id: settings.defaultTimeZoneId || '',
		website_url: ''
	}
});

module.exports = Venue;
