/* jshint browserify: true */

var TracksListItemTitle,
	_ = require( 'underscore' ),
	l10n = require( 'bandstand' ).l10n,
	wp = require( 'wp' );

TracksListItemTitle = wp.media.View.extend({
	tagName: 'span',
	className: 'bandstand-tracks-list-item-title',

	initialize: function( options ) {
		this.model = options.model;
	},

	render: function() {
		var title = this.model.get( 'title' ),
			text = title;

		if ( _.isEmpty( text ) ) {
			text = l10n.addTrackTitle || 'Add a title...';
		}

		this.$el.text( text ).toggleClass( 'is-empty', _.isEmpty( title ) );

		return this;
	}
});

module.exports = TracksListItemTitle;
