/* jshint browserify: true */

var SearchResults,
	Backbone = require( 'backbone' ),
	SearchResult = require( '../models/search-result' );

SearchResults = Backbone.Collection.extend({
	model: SearchResult
});

module.exports = SearchResults;
