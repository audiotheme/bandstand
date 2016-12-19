/* jshint browserify: true */

var $ = require( 'jquery' ),
	_ = require( 'underscore' ),
	app = require( 'bandstand' );

function ITunes() {
	if ( ! ( this instanceof ITunes ) ) {
		return new ITunes();
	}
}

function formatReleaseDate( dateString ) {
	var date = new Date( dateString );

	return date.getFullYear() + '/' + ( date.getMonth() + 1 ) + '/' + date.getDate();
}

function transformAlbum( item ) {
	return {
		id: item.collectionId,
		artist: item.artistName,
		artworkUrl: item.artworkUrl60,
		externalUrl: item.collectionViewUrl,
		releaseDate: formatReleaseDate( item.releaseDate ),
		source: 'itunes',
		title: item.collectionName,
		trackCount: item.trackCount
	};
}

function transformTrack( item ) {
	return {
		itunes_id: item.trackId,
		artist: item.artistName,
		duration: app.util.formatTime( item.trackTimeMillis / 1000 ),
		purchase_url: item.trackViewUrl,
		source: 'itunes',
		stream_url: item.previewUrl,
		title: item.trackName,
		track_number: String( item.trackNumber )
	};
}

ITunes.prototype.getTracks = function( id ) {
	var deferred = $.Deferred();

	$.ajax({
		url: 'https://itunes.apple.com/lookup?callback=?',
		dataType: 'json',
		data: {
			id: id,
			entity: 'song'
		}
	}).done(function( response ) {
		var tracks = _.chain( response.results ).where({ kind: 'song' }).map(function( item ) {
			return transformTrack( item );
		}).value();

		deferred.resolve( tracks );
	}).fail(function( response ) {
		deferred.reject( response );
	});

	return deferred.promise();
};

ITunes.prototype.search = function( query ) {
	var deferred = $.Deferred();

	$.ajax({
		url: 'https://itunes.apple.com/search?callback=?',
		dataType: 'json',
		data: {
			term: query,
			media: 'music',
			entity: 'album'
		}
	}).done(function( response ) {
		var albums = _.map( response.results, function( item ) {
			return transformAlbum( item );
		});

		deferred.resolve( albums );
	}).fail(function( response ) {
		deferred.reject( response );
	});

	return deferred.promise();
};

module.exports = new ITunes();
