/* jshint browserify: true */

var MediaImport,
	_ = require( 'underscore' ),
	l10n = require( 'bandstand' ).l10n,
	wp = require( 'wp' );

MediaImport = wp.media.controller.Library.extend({
	defaults: _.defaults({
		title: l10n.importFromMediaLibrary || 'Import from Media Library',
		menu: 'default',
		menuItem: {
			text: l10n.importFromMedia || 'Import from Media',
			priority: 120
		},
		toolbar: 'media-import',
		allowLocalEdits: true,
		editable: false,
		displaySettings: false,
		displayUserSettings: false,
		filterable: 'uploaded',
		library: wp.media.query({ type: 'audio' }),
		multiple: 'add'
	}, wp.media.controller.Library.prototype.defaults )
});

module.exports = MediaImport;
