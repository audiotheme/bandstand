/* jshint browserify: true */
/* globals _bandstandTracks */

'use strict';

var $ = require( 'jquery' ),
	cuebone = require( 'cuebone' );

$( '.bandstand-tracklist' ).each(function() {
	var $tracklist = $( this );

	var tracks = new cuebone.collection.Tracks( _bandstandTracks, {
		id: 'tracklist-tracks'
	});

	var player = new cuebone.controller.Player({
		id: 'tracklist-player'
	}, {
		events: cuebone.Events,
		tracks: tracks
	});

	cuebone.players.add( player );

	$tracklist.on( 'click', '.bandstand-track', function( e ) {
		var index = $tracklist.find( '.bandstand-track' ).index( this );
		player.setCurrentTrack( index );

		if ( 'playing' === player.media.first().get( 'status' ) ) {
			player.pause();
		} else {
			player.play();
		}
	});
});
