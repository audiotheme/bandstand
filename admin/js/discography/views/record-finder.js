/* jshint browserify: true */

var RecordFinder,
	SearchGroup = require( './finder/search-group' ),
	SearchResults = require( './finder/search-results' ),
	ServicesPanel = require( './finder/services-panel' ),
	wp = require( 'wp' );

RecordFinder = wp.media.View.extend({
	className: 'bandstand-record-finder',

	initialize: function( options ) {
		this.controller = options.controller;
	},

	render: function() {
		var state = this.controller.state();

		this.views.set([
			new SearchGroup({
				controller: state,
				collection: state.get( 'results' ),
				selection: state.get( 'selection' )
			}),
			new ServicesPanel({
				controller: state
			}),
			new SearchResults({
				collection: state.get( 'results' ),
				selection: state.get( 'selection' )
			})
		]);

		return this;
	}
});

module.exports = RecordFinder;
