/* jshint browserify: true */

var $ = require( 'jquery' ),
	_ = require( 'underscore' ),
	app = require( 'bandstand' );

function Spotify() {
	if ( ! ( this instanceof Spotify ) ) {
		return new Spotify();
	}
}

function normalizeArtist( artists ) {
	var names = [];

	_.each( artists, function( artist ) {
		names.push( artist.name );
	});

	return names.join( ', ' );
}

function transformAlbum( item ) {
	return {
		id: item.id,
		artworkUrl: item.images[2].url,
		externalUrl: item.href,
		source: 'spotify',
		title: item.name
	};
}

function transformTrack( item ) {
	return {
		spotify_id: item.id,
		artist: normalizeArtist( item.artists ),
		duration: app.util.formatTime( item.duration_ms / 1000 ),
		source: 'spotify',
		stream_url: item.preview_url,
		title: item.name,
		track_number: String( item.track_number )
	};
}

Spotify.prototype.getTracks = function( id ) {
	var deferred = $.Deferred();

	$.ajax({
		url: 'https://api.spotify.com/v1/albums/' + id,
		dataType: 'json'
	}).done(function( response ) {
		var tracks = _.map( response.tracks.items, function( item ) {
			return transformTrack( item );
		});

		deferred.resolve( tracks );
	}).fail(function( response ) {
		deferred.reject( response );
	});

	return deferred.promise();
};

Spotify.prototype.search = function( query ) {
	var deferred = $.Deferred();

	$.ajax({
		url: 'https://api.spotify.com/v1/search',
		data: {
			q: query,
			type: 'album'
		}
	}).done(function( response ) {
		var albums = _.map( response.albums.items, function( item ) {
			return transformAlbum( item );
		});

		deferred.resolve( albums );
	}).fail(function( response ) {
		deferred.reject( response );
	});

	return deferred.promise();
};

module.exports = new Spotify();
