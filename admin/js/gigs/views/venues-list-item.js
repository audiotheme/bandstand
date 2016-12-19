/* jshint browserify: true */

var VenuesListItem,
	wp = require( 'wp' );

VenuesListItem = wp.media.View.extend({
	tagName: 'li',
	className: 'bandstand-venues-list-item',

	events: {
		'click': 'setSelection'
	},

	initialize: function( options ) {
		this.controller = options.controller;
		this.model = options.model;
		this.selection = options.selection;

		this.selection.on( 'reset', this.updateSelected, this );
		this.listenTo( this.model, 'change:name', this.render );
	},

	render: function() {
		this.$el.html( this.model.get( 'name' ) );
		this.updateSelected();
		return this;
	},

	setSelection: function() {
		this.selection.reset( this.model );
	},

	updateSelected: function() {
		var state = this.controller.state( 'venues' ),
			isSelected = this.selection.first() === this.model;

		this.$el.toggleClass( 'is-selected', isSelected );

		if ( isSelected ) {
			state.set( 'selectedItem', this.$el );
		}
	}
});

module.exports = VenuesListItem;
