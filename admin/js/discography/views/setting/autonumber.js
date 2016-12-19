/* jshint browserify: true */

var AutoNumber,
	_ = require( 'underscore' ),
	l10n = require( 'bandstand' ).l10n,
	wp = require( 'wp' );

AutoNumber = wp.media.View.extend({
	className: 'bandstand-tracklist-autonumber-setting',
	tagName: 'span',

	events: {
		'change input': 'updateAutoNumbering'
	},

	initialize: function( options ) {
		this.controller = options.controller;
	},

	render: function() {
		var template = _.template( '<label><input type="checkbox"> <%= text %></label>' );

		this.$el.html( template({ text: l10n.autoNumber || 'Auto-number' }) );
		this.$checkbox = this.$( 'input[type="checkbox"]' );
		this.$checkbox.prop( 'checked', this.controller.state().get( 'autoNumber' ) );

		return this;
	},

	updateAutoNumbering: function() {
		this.controller.state().set( 'autoNumber', this.$checkbox.prop( 'checked' ) );
	}
});

module.exports = AutoNumber;
