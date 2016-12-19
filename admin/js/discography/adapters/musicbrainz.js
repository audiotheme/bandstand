/* jshint browserify: true */

var $ = require( 'jquery' ),
	_ = require( 'underscore' ),
	app = require( 'bandstand' );

function MusicBrainz() {
	if ( ! ( this instanceof MusicBrainz ) ) {
		return new MusicBrainz();
	}
}

function normalizeArtist( artists ) {
	var text = '';

	_.each( artists, function( credit ) {
		text += credit.artist.name;

		if ( ! _.isUndefined( credit.joinphrase ) ) {
			text += credit.joinphrase;
		}
	});

	return text;
}

function transformRelease( item ) {
	return {
		id: item.id,
		asin: item.asin,
		artist: normalizeArtist( item['artist-credit'] ),
		externalUrl: 'https://musicbrainz.org/release/' + item.id,
		recordLabel: 'label-info' in item ? item['label-info'][0].label.name : '',
		releaseDate: ( item.date || '' ).replace( /-/g, '/' ),
		source: 'musicbrainz',
		title: item.title,
		trackCount: item['track-count']
	};
}

function transformTrack( item ) {
	return {
		musicbrainz_id: item.id,
		artist: normalizeArtist( item['artist-credit'] ),
		duration: app.util.formatTime( item.length / 1000 ),
		source: 'musicbrainz',
		title: item.title,
		track_number: String( item.number )
	};
}

MusicBrainz.prototype.getTracks = function( id ) {
	var deferred = $.Deferred();

	$.ajax({
		url: 'https://musicbrainz.org/ws/2/release/' + id,
		dataType: 'json',
		data: {
			inc: 'artist-credits recordings',
			fmt: 'json'
		}
	}).done(function( response ) {
		var tracks = _.map( response.media[0].tracks, function( item ) {
			return transformTrack( item );
		});

		deferred.resolve( tracks );
	}).fail(function( response ) {
		deferred.reject( response );
	});

	return deferred.promise();
};

MusicBrainz.prototype.search = function( query ) {
	var deferred = $.Deferred();

	$.ajax({
		url: 'https://musicbrainz.org/ws/2/release/',
		dataType: 'json',
		data: {
			query: query,
			fmt: 'json'
		}
	}).done(function( response ) {
		var releases = _.map( response.releases, function( item ) {
			return transformRelease( item );
		});

		deferred.resolve( releases );
	}).fail(function( response ) {
		deferred.reject( response );
	});

	return deferred.promise();
};

module.exports = new MusicBrainz();
