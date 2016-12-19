/* jshint browserify: true */

var TrackMeta,
	wp = require( 'wp' );

TrackMeta = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-track-meta',
	template: wp.template( 'bandstand-track-meta' ),

	events: {
		'click .js-delete-track': 'removeTrack'
	},

	initialize: function( options ) {
		this.controller = options.controller;
		this.model = options.model;
	},

	render: function() {
		this.$el.empty();

		if ( this.model ) {
			this.$el.html( this.template( this.model.toJSON() ) );
		}

		return this;
	},

	/**
	 * Remove the track.
	 *
	 * Moves the track model from the main collection to a 'deleted' collection
	 * so it can be destroyed when the tracklist is saved.
	 */
	removeTrack: function() {
		var state = this.controller.state();

		state.get( 'collection' ).remove( this.model );
		state.get( 'deleted' ).add( this.model );
		state.get( 'selection' ).remove( this.model );
	}
});

module.exports = TrackMeta;
