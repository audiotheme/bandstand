/* jshint browserify: true */

var TracklistManager,
	TrackPanel = require( './track/panel' ),
	TracksList = require( './tracks/list' ),
	wp = require( 'wp' );

TracklistManager = wp.media.View.extend({
	className: 'bandstand-tracklist-manager',

	initialize: function( options ) {
		this.controller = options.controller;
		this.collection = options.collection;
		this.selection = options.selection;

		if ( ! this.collection.length ) {
			this.collection.add({
				record_id: this.collection.recordId,
				status: 'publish'
			});

			this.controller.state().set( 'isDirty', false );
		}

		this.selection.reset( this.collection.first() );
	},

	render: function() {
		this.views.add([
			new TracksList({
				collection: this.collection,
				selection: this.selection
			}),
			new TrackPanel({
				controller: this.controller,
				selection: this.selection
			})
		]);

		return this;
	}
});

module.exports = TracklistManager;
