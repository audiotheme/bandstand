/* jshint browserify: true */

var VenuesController,
	Backbone = require( 'backbone' ),
	l10n = require( 'bandstand' ).l10n,
	Venues = require( '../collections/venues' ),
	wp = require( 'wp' );

VenuesController = wp.media.controller.State.extend({
	defaults: {
		id: 'venues',
		title: l10n.venues || 'Venues',
		button: {
			text: l10n.select || 'Select'
		},
		content: 'venues-manager',
		menu: 'default',
		menuItem: {
			text: l10n.manageVenues || 'Manage Venues',
			priority: 10
		},
		mode: 'view',
		provider: 'venues',
		toolbar: 'venues'
	},

	initialize: function( options ) {
		var results = options.results || new Venues(),
			venues = options.venues || new Venues();

		this.set( 'results', results );
		this.set( 'venues', venues );
		this.set( 'selection', new Venues() );
		this.set( 'selectedItem', Backbone.$() );

		// Synchronize changes to models in each collection.
		results.observe( venues );
		venues.observe( results );
	},

	next: function() {
		var provider = this.get( 'provider' ),
			collection = this.get( provider ),
			currentIndex = collection.indexOf( this.get( 'selection' ).at( 0 ) );

		if ( collection.length - 1 !== currentIndex ) {
			this.get( 'selection' ).reset( collection.at( currentIndex + 1 ) );
		}
	},

	previous: function() {
		var provider = this.get( 'provider' ),
			collection = this.get( provider ),
			currentIndex = collection.indexOf( this.get( 'selection' ).at( 0 ) );

		if ( 0 !== currentIndex ) {
			this.get( 'selection' ).reset( collection.at( currentIndex - 1 ) );
		}
	},

	search: function( query ) {
		// Restore the original state if the text in the search field
		// is less than 3 characters.
		if ( query.length < 3 ) {
			this.get( 'results' ).reset();
			this.set( 'provider', 'venues' );
			return;
		}

		this.set( 'provider', 'results' );
		this.get( 'results' ).fetch({
			data: {
				search: query
			},
			reset: true
		});
	}
});

module.exports = VenuesController;
