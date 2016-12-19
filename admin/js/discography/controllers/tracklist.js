/* jshint browserify: true */

var Tracklist,
	Backbone = require( 'backbone' ),
	l10n = require( 'bandstand' ).l10n,
	RecordTracks = require( '../collections/record-tracks' ),
	settings = require( 'bandstand' ).settings(),
	wp = require( 'wp' );

Tracklist = wp.media.controller.State.extend({
	defaults: {
		id: 'tracklist',
		title: l10n.tracklist || 'Tracklist',
		autoNumber: false,
		button:  {
			text: l10n.addTrack || 'Add Track'
		},
		collection: null,
		content: 'bandstand-tracklist',
		deleted: null,
		isDirty: false,
		menu: 'default',
		menuItem: {
			text: l10n.manageTracklist || 'Manage Tracklist',
			priority: 10
		},
		selection: null,
		toolbar: 'tracklist',
		uploader: true
	},

	initialize: function( options ) {
		var collection = options.collection || new RecordTracks(),
			selection = options.selection || new Backbone.Collection();

		this.set( 'autoNumber', options.autoNumber || false );
		this.set( 'collection', collection );
		this.set( 'deleted', options.deleted || new Backbone.Collection() );
		this.set( 'selection', selection );

		this.listenTo( this, 'change:autoNumber', this.persistAutoNumber );
		this.listenTo( this, 'change:autoNumber', this.updateTrackNumbers );
		this.listenTo( this.get( 'collection' ), 'add remove reset change:menu_order', this.updateTrackNumbers );
		this.listenTo( this.get( 'collection' ), 'add remove reset change', this.updateDirtyState );
		this.listenTo( this.get( 'selection' ), 'remove', this.updateSelection );
	},

	next: function() {
		var collection = this.get( 'collection' ),
			currentIndex = collection.indexOf( this.get( 'selection' ).at( 0 ) );

		if ( collection.length - 1 !== currentIndex ) {
			this.get( 'selection' ).reset( collection.at( currentIndex + 1 ) );
		}
	},

	previous: function() {
		var collection = this.get( 'collection' ),
			currentIndex = collection.indexOf( this.get( 'selection' ).at( 0 ) );

		if ( 0 !== currentIndex ) {
			this.get( 'selection' ).reset( collection.at( currentIndex - 1 ) );
		}
	},

	persistAutoNumber: function() {
		wp.ajax.post( 'bandstand_ajax_update_tracklist_autonumber', {
			autonumber: this.get( 'autoNumber' ) ? 1 : 0,
			record_id: settings.record.id,
			nonce: settings.autoNumberNonce
		}).done(function( response ) {

		});
	},

	updateDirtyState: function() {
		this.set( 'isDirty', true );
	},

	updateSelection: function() {
		var collection = this.get( 'collection' );

		if ( collection.length ) {
			this.get( 'selection' ).reset( collection.last() );
		}
	},

	updateTrackNumbers: function() {
		if ( ! this.get( 'autoNumber' ) ) {
			return;
		}

		this.get( 'collection' ).each(function( track, index ) {
			track.set( 'track_number', index + 1 );
		});
	}
});

module.exports = Tracklist;
