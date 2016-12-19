/* jshint browserify: true */

var ServicesPanel,
	wp = require( 'wp' );

ServicesPanel = wp.media.View.extend({
	className: 'bandstand-record-finder-services-panel',
	template: wp.template( 'bandstand-record-finder-services-panel' ),

	events: {
		'change select': 'updateService'
	},

	initialize: function( options ) {
		this.controller = options.controller;
	},

	render: function() {
		var services = this.controller.get( 'services' );

		this.$el.html( this.template({
			selected: this.controller.get( 'service' ).get( 'id' ),
			services: services.toJSON()
		}) );

		this.$dropdown = this.$( 'select' );

		return this;
	},

	updateService: function() {
		var serviceId = this.$dropdown.val(),
			services = this.controller.get( 'services' );

		this.controller.set( 'service', services.get( serviceId ) );
	}
});

module.exports = ServicesPanel;
