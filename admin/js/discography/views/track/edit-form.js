/* jshint browserify: true */

var TrackEditForm,
	_ = require( 'underscore' ),
	templateHelpers = require( '../../../modules/template-helpers' ),
	wp = require( 'wp' );

TrackEditForm = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-track-edit-form',
	template: wp.template( 'bandstand-track-edit-form' ),

	initialize: function( options ) {
		this.model = options.model;
		this.listenTo( this.model, 'change:track_number', this.updateTrackNumber );
	},

	render: function() {
		var data = _.extend( this.model.toJSON(), templateHelpers );
		this.$el.html( this.template( data ) );
		return this;
	},

	updateTrackNumber: function() {
		this.$( 'input[data-setting="track_number"]' ).val( this.model.get( 'track_number' ) );
	}
});

module.exports = TrackEditForm;
