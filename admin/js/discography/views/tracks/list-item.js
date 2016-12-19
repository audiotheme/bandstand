/* jshint browserify: true */

var TracksListItem,
	TracksListItemNumber = require( './list-item-number' ),
	TracksListItemTitle = require( './list-item-title' ),
	wp = require( 'wp' );

TracksListItem = wp.media.View.extend({
	tagName: 'li',
	className: 'bandstand-tracks-list-item',

	events: {
		'click': 'setSelection'
	},

	initialize: function( options ) {
		this.model = options.model;
		this.selection = options.selection;

		this.selection.on( 'reset', this.updateSelected, this );
		this.listenTo( this.model, 'destroy', this.remove );
		this.listenTo( this.model, 'change:title change:track_number', this.render );
	},

	render: function() {
		this.views.set([
			new TracksListItemNumber({
				model: this.model
			}),
			new TracksListItemTitle({
				model: this.model
			})
		]);

		this.updateSelected();

		return this;
	},

	remove: function() {
		this.$el.remove();
	},

	setSelection: function() {
		this.selection.reset( this.model );
	},

	updateSelected: function() {
		var isSelected = this.selection.first() === this.model;
		this.$el.toggleClass( 'is-selected', isSelected );
	}
});

module.exports = TracksListItem;
