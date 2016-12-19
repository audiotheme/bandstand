/* jshint browserify: true */

var SearchResult,
	wp = require( 'wp' );

SearchResult = wp.Backbone.View.extend({
	className: 'bandstand-record-finder-result',
	tagName: 'li',
	template: wp.template( 'bandstand-record-finder-result' ),

	events: {
		'click': 'setSelection'
	},

	initialize: function( options ) {
		this.model = options.model;
		this.selection = options.selection;

		this.selection.on( 'reset', this.updateSelected, this );
	},

	render: function() {
		this.$el.html( this.template( this.model.toJSON() ) );
		return this;
	},

	setSelection: function() {
		if ( this.selection.first() === this.model ) {
			this.selection.reset();
		} else {
			this.selection.reset( this.model );
		}
	},

	updateSelected: function() {
		this.$el.toggleClass( 'is-selected', this.selection.first() === this.model );
	}
});

module.exports = SearchResult;
