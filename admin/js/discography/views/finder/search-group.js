/* jshint browserify: true */

var SearchGroup,
	wp = require( 'wp' );

SearchGroup = wp.media.View.extend({
	className: 'bandstand-record-finder-search-group',
	template: wp.template( 'bandstand-record-finder-search-group' ),

	events: {
		'click button': 'search',
		'keyup input': 'updateQuery',
		'keypress input': 'search'
	},

	initialize: function( options ) {
		this.controller = options.controller;
		this.collection = options.collection;
		this.selection = options.selection;
	},

	render: function() {
		this.$el.html( this.template() );
		this.$button = this.$( 'button' );
		this.$field = this.$( 'input' ).val( this.controller.get( 'query' ) );
		return this;
	},

	search: function( e ) {
		var $button;

		if ( 'keypress' === e.type && 13 !== e.which ) {
			return;
		}

		$button = this.$button.prop( 'disabled', true );

		this.controller.search().always(function() {
			$button.prop( 'disabled', false );
		});
	},

	updateQuery: function() {
		this.controller.set( 'query', this.$field.val() );
	}
});

module.exports = SearchGroup;
