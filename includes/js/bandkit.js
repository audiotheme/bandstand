/* jshint browserify: true */

'use strict';

var _ = require( 'underscore' ),
	app = require( 'bandstand' ),
	cuebone = require( 'cuebone' );

_.extend( app, {
	players: cuebone.players
});
