/* jshint browserify: true */
/* globals _bandstandTracklistSettings */

var collection, frame, services, settings, tracklist, Tracklist, TracklistItem,
	$ = require( 'jquery' ),
	app = require( 'bandstand' ),
	Backbone = require( 'backbone' ),
	RecordTracks = require( './discography/collections/record-tracks' ),
	TracklistFrame = require( './discography/views/frame/tracklist' ),
	wp = require( 'wp' );

settings = app.settings( _bandstandTracklistSettings );

collection = new RecordTracks( settings.tracks, {
	recordId: settings.record.id
});

services = new Backbone.Collection([
	/*{
		id: 'itunes',
		name: 'iTunes',
		adapter: require( './discography/adapters/itunes' )
	},*/
	{
		id: 'musicbrainz',
		name: 'MusicBrainz',
		adapter: require( './discography/adapters/musicbrainz' )
	}/*,
	{
		id: 'spotify',
		name: 'Spotify',
		adapter: require( './discography/adapters/spotify' )
	}*/
]);

// Initialize the tracklist frame.
frame = new TracklistFrame({
	autoNumberTracklist: settings.autoNumberTracklist,
	collection: collection,
	record: settings.record,
	services: services
});

frame.on( 'open', function() {
	var state = frame.state( 'tracklist' ),
		collection = state.get( 'collection' ),
		selection = state.get( 'selection' );

	if ( ! selection.length && collection.length ) {
		selection.reset( collection.first() );
	}
});

frame.on( 'close', function() {
	collection.fetch({
		data: {
			context: 'edit',
			record: settings.record.id
		}
	}).done(function( tracks ) {
		collection.reset( tracks );
		frame.state( 'tracklist' ).get( 'selection' ).reset();
	});
});

// Set the extensions that can be uploaded.
// jscs:disable
/*
frame.uploader.options.uploader.plupload = {
	filters: {
		mime_types: [{
			title: 'Audio Files',
			extensions: 'm4a,mp3,ogg,wma'
		}]
	}
};
*/
// jscs:enable

Tracklist = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-tracklist',

	initialize: function( options ) {
		this.collection = options.collection;

		this.listenTo( this.collection, 'add', this.addTrack );
		this.listenTo( this.collection, 'reset', this.render );
	},

	render: function() {
		this.$list = this.$el.html( '<ul />' ).find( 'ul' );

		if ( this.collection.length ) {
			this.collection.each( this.addTrack, this );
		} else {
			// @todo Show feedback about there not being any matches.
		}

		return this;
	},

	addTrack: function( model ) {
		var view = new TracklistItem({
			controller: this.controller,
			model: model
		});

		this.views.add( 'ul', view, {
			at: this.collection.indexOf( model )
		});
	}
});

TracklistItem = wp.media.View.extend({
	tagName: 'li',
	className: 'bandstand-tracklist-item',

	events: {
		'dblclick': 'editTrack'
	},

	initialize: function( options ) {
		this.controller = options.controller;
		this.model = options.model;

		this.listenTo( this.model, 'destroy', this.remove );
		this.listenTo( this.model, 'change:title change:track_number', this.render );
	},

	render: function() {
		var text = '',
			trackNumber = this.model.get( 'track_number' );

		if ( trackNumber ) {
			text = trackNumber + '. ';
		}

		text += this.model.get( 'title' );
		this.$el.html( text );

		return this;
	},

	editTrack: function( e ) {
		e.preventDefault();

		frame.state( 'tracklist' ).get( 'selection' ).reset( this.model );
		frame.open();
	},

	remove: function() {
		this.$el.remove();
	}
});

tracklist = new Tracklist({
	el: document.getElementById( 'bandstand-tracklist' ),
	collection: collection
});

tracklist.render();

$( '.js-open-tracklist-manager' ).on( 'click', function( e ) {
	e.preventDefault();
	frame.open();
});

// Set up the record links repeater.
$( document ).ready(function() {
	$( '#record-links' ).bandstandRepeater();
});
