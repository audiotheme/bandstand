/* jshint browserify: true */

var VenuesContent,
	VenuePanel = require( '../venue/panel' ),
	VenuesList = require( '../venues-list' ),
	VenuesSearch = require( '../venues-search' ),
	wp = require( 'wp' );

VenuesContent = wp.media.View.extend({
	className: 'bandstand-venue-frame-content',

	initialize: function( options ) {
		var collection = options.collection,
			selection = options.selection;

		this.controller = options.controller;
		this.collection = options.collection;
		this.results = options.results;
		this.selection = options.selection;

		if ( ! this.collection.length ) {
			this.collection.fetch({
				data: {
					context: 'edit'
				},
				remove: false
			}).done(function() {
				if ( ! selection.length ) {
					selection.reset( collection.first() );
				}
			});
		}
	},

	render: function() {
		this.views.add([
			new VenuesSearch({
				controller: this.controller
			}),
			new VenuesList({
				controller: this.controller,
				collection: this.collection,
				results: this.results,
				selection: this.selection
			}),
			new VenuePanel({
				controller: this.controller,
				selection: this.selection
			})
		]);

		return this;
	}
});

module.exports = VenuesContent;
