/* jshint browserify: true */

var VenuePanel,
	VenueDetails = require( './details' ),
	VenueEditForm = require( './edit-form' ),
	VenuePanelTitle = require( './panel-title' ),
	wp = require( 'wp' );

VenuePanel = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-venue-panel',

	initialize: function( options ) {
		this.controller = options.controller;
		this.selection = options.selection;

		this.listenTo( this.selection, 'reset', this.render );
		this.listenTo( this.controller.state(), 'change:mode', this.render );
	},

	render: function() {
		var panelContent,
			model = this.selection.first();

		if ( ! this.selection.length ) {
			return this;
		}

		if ( 'edit' === this.controller.state().get( 'mode' ) ) {
			panelContent = new VenueEditForm({
				model: model
			});
		} else {
			panelContent = new VenueDetails({
				model: model
			});
		}

		this.views.set([
			new VenuePanelTitle({
				controller: this.controller,
				model: model
			}),
			panelContent
		]);

		return this;
	}
});

module.exports = VenuePanel;
