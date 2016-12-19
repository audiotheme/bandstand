/* jshint browserify: true */

var TracksListItemNumber,
	wp = require( 'wp' );

TracksListItemNumber = wp.media.View.extend({
	tagName: 'span',
	className: 'bandstand-tracks-list-item-number',

	initialize: function( options ) {
		this.model = options.model;
	},

	render: function() {
		var text = this.model.get( 'track_number' );

		if ( text ) {
			text += '. ';
		}

		this.$el.text( text );

		return this;
	}
});

module.exports = TracksListItemNumber;
