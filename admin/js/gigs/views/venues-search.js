/* jshint browserify: true */

var VenuesSearch,
	wp = require( 'wp' );

VenuesSearch = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-venues-search',
	template: wp.template( 'bandstand-venues-search-field' ),

	events: {
		'keyup input': 'search',
		'search input': 'search'
	},

	render: function() {
		this.$field = this.$el.html( this.template() ).find( 'input' );
		return this;
	},

	search: function() {
		var view = this,
			state = this.controller.state();

		clearTimeout( this.timeout );
		this.timeout = setTimeout(function() {
			state.search( view.$field.val() );
		}, 300 );
	}
});

module.exports = VenuesSearch;
