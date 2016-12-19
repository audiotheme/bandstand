/* jshint browserify: true */

var RecordFinderToolbar,
	_ = require( 'underscore' ),
	l10n = require( 'bandstand' ).l10n,
	wp = require( 'wp' );

RecordFinderToolbar = wp.media.view.Toolbar.extend({
	initialize: function( options ) {
		this.controller = options.controller;

		_.bindAll( this, 'importRecord' );

		// This is a button.
		this.options.items = _.defaults( this.options.items || {}, {
			import: {
				text: l10n.importRecord || 'Import Record',
				style: 'primary',
				priority: 80,
				requires: {
					selection: true
				},
				click: this.importRecord
			},
			spinner: new wp.media.view.Spinner({
				priority: 100
			})
		});

		this.options.items.spinner.delay = 0;
		wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
	},

	importRecord: function() {
		var spinner = this.get( 'spinner' ).show();

		this.controller.state().importRecord().done(function() {
			spinner.hide();
		});
	}
});

module.exports = RecordFinderToolbar;
