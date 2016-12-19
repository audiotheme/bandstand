/* jshint browserify: true */

var TracksList,
	_ = require( 'underscore' ),
	TracksListItem = require( './list-item' ),
	wp = require( 'wp' );

TracksList = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-tracks',

	initialize: function( options ) {
		this._viewsByCid = {};

		this.collection = options.collection;
		this.selection = options.selection;

		this.listenTo( this.collection, 'add', this.addTrack );
		this.listenTo( this.collection, 'remove', this.removeTrack );
		this.listenTo( this.collection, 'reset', this.render );
	},

	render: function() {
		this.$list = this.$el.html( '<ul />' ).find( 'ul' );

		if ( this.collection.length ) {
			this.collection.each( this.addTrack, this );
		} else {
			// @todo Show feedback about there not being any matches.
		}

		this.initializeSortable();

		return this;
	},

	addTrack: function( model ) {
		this.views.add( 'ul', this.createTrackView( model ), {
			at: this.collection.indexOf( model )
		});
	},

	createTrackView: function( model ) {
		var view = new TracksListItem({
			model: model,
			selection: this.selection
		});

		return this._viewsByCid[ model.cid ] = view;
	},

	initializeSortable: function() {
		var collection = this.collection;

		this.$list.sortable({
			axis: 'y',
			delay: 150,
			forceHelperSize: true,
			forcePlaceholderSize: true,
			opacity: 0.6,
			start: function( e, ui ) {
				ui.placeholder.css( 'visibility', 'visible' );
				ui.item.data( 'sortableIndexStart', ui.item.index() );
			},
			update: _.bind(function( e, ui ) {
				var model = collection.at( ui.item.data( 'sortableIndexStart' ) ),
					comparator = collection.comparator;

				// Temporarily disable the comparator to prevent `add` from re-sorting.
				delete collection.comparator;

				// Silently shift the model to its new index.
				collection.remove( model, {
					silent: true
				});

				collection.add( model, {
					silent: true,
					at: ui.item.index()
				});

				// Restore the comparator.
				collection.comparator = comparator;

				// Update the menu order for each model.
				collection.each(function( model, index ) {
					model.set( 'menu_order', index + 1 );
				});

				// Fire the `reset` event to ensure other collections sync.
				collection.trigger( 'reset', collection );
			}, this )
		});
	},

	removeTrack: function( model ) {
		var view = this._viewsByCid[ model.cid ];
		delete this._viewsByCid[ model.cid ];

		if ( view ) {
			view.remove();
		}
	}
});

module.exports = TracksList;
