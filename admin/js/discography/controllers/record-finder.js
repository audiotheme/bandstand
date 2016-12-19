/* jshint browserify: true */
/* globals localStorage */

var RecordFinder,
	_ = require( 'underscore' ),
	l10n = require( 'bandstand' ).l10n,
	storage = localStorage || {},
	SearchResults = require( '../collections/search-results' ),
	wp = require( 'wp' );

RecordFinder = wp.media.controller.State.extend({
	defaults: {
		id: 'record-finder',
		title: l10n.importFromService || 'Import from Service',
		collection: null,
		content: 'record-finder',
		frame: null,
		menu: 'default',
		menuItem: {
			text: l10n.importFromService || 'Import from Service',
			priority: 200
		},
		query: '',
		results: null,
		selection: null,
		service: null,
		services: null,
		toolbar: 'record-finder'
	},

	initialize: function() {
		var serviceId = storage.bandstandRecordFinderServiceId,
			services = this.get( 'services' );

		this.set( 'results', new SearchResults() );
		this.set( 'selection', new SearchResults() );

		serviceId = serviceId || services.first().get( 'id' );
		this.set( 'service', services.get( serviceId ) );

		this.listenTo( this, 'change:service', this.persistService );
	},

	importRecord: function() {
		var controller = this.get( 'frame' ),
			collection = this.get( 'collection' ),
			selection = this.get( 'selection' ),
			id = selection.first().get( 'id' ),
			model = collection.first();

		// Remove the default track if it hasn't been updated.
		if (
			1 === collection.length &&
			! model.get( 'id' ) && ! model.get( 'title' )
		) {
			collection.remove( model );
		}

		return this.get( 'service' ).get( 'adapter' )
			.getTracks( id )
			.done(function( tracks ) {
				_.each( tracks, function( track ) {
					track = _.extend( track, {
						menu_order: collection.length,
						record_id: collection.recordId,
						status: 'publish'
					});

					collection.add( track );
				});

				selection.reset();
				controller.setState( 'tracklist' );
			});
	},

	persistService: function() {
		storage.bandstandRecordFinderServiceId = this.get( 'service' ).get( 'id' );
	},

	search: function() {
		var collection = this.get( 'results' );

		this.get( 'selection' ).reset();

		return this.get( 'service' ).get( 'adapter' )
			.search( this.get( 'query' ) )
			.done(function( releases ) {
				collection.reset( releases );
			});
	}
});

module.exports = RecordFinder;
