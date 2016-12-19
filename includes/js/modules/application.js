/* jshint browserify: true */

'use strict';

var _ = require( 'underscore' ),
	utils = require( './utils' );

function Application() {
	var settings = {};

	_.extend( this, {
		collection: {},
		controller: {},
		l10n: {},
		model: {},
		util: utils,
		view: {}
	});

	this.settings = function( options ) {
		if ( options ) {
			_.extend( settings, options );
		}

		if ( settings.l10n ) {
			this.l10n = _.extend( this.l10n, settings.l10n );
			delete settings.l10n;
		}

		return settings || {};
	};
}

global.bandstand = global.bandstand || new Application();
module.exports = global.bandstand;
