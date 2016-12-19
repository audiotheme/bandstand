/* jshint browserify: true */

var TrackSidebar,
	TrackMeta = require( './meta' ),
	wp = require( 'wp' );

TrackSidebar = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-track-sidebar',

	initialize: function( options ) {
		this.controller = options.controller;
		this.selection = options.selection;

		this.listenTo( this.selection, 'reset', this.render );
	},

	render: function() {
		this.views.set([
			new TrackMeta({
				controller: this.controller,
				model: this.selection.first()
			})
		]);

		return this;
	}
});

module.exports = TrackSidebar;
