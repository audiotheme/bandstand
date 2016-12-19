/* jshint browserify: true */

var SearchResults,
	SearchResult = require( './search-result' ),
	wp = require( 'wp' );

SearchResults = wp.Backbone.View.extend({
	tagName: 'ul',

	initialize: function( options ) {
		this.collection = options.collection;
		this.selection = options.selection;

		this.listenTo( this.collection, 'reset', this.render );
		this.listenTo( this.collection, 'add', this.addItem );
	},

	render: function() {
		this.$el.empty();

		if ( this.collection.length ) {
			this.collection.each( this.addItem, this );
		}

		return this;
	},

	addItem: function( model ) {
		this.views.add(
			new SearchResult({
				model: model,
				selection: this.selection
			})
		);
	}
});

module.exports = SearchResults;
