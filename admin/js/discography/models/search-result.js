/* jshint browserify: true */

var SearchResult,
	Backbone = require( 'backbone' );

SearchResult = Backbone.Model.extend({
	defaults: {
		id: '',
		artist: '',
		title: ''
	}
});

module.exports = SearchResult;
