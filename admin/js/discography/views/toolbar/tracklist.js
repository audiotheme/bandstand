/* jshint browserify: true */

var TracklistToolbar,
	_ = require( 'underscore' ),
	AutoNumberSetting = require( '../setting/autonumber' ),
	l10n = require( 'bandstand' ).l10n,
	wp = require( 'wp' );

TracklistToolbar = wp.media.view.Toolbar.extend({
	initialize: function( options ) {
		var state = options.controller.state();
		this.controller = options.controller;

		_.bindAll( this, 'addTrack', 'saveTracklist' );

		// These are buttons.
		this.options.items = _.defaults( this.options.items || {}, {
			add: {
				text: this.controller.state().get( 'button' ).text,
				style: 'secondary',
				priority: -80,
				click: this.addTrack
			},
			autonumber: new AutoNumberSetting({
				controller: this.controller,
				priority: -60
			}),
			save: {
				text: l10n.saveTracklist || 'Save Tracklist',
				style: 'primary',
				priority: 80,
				click: this.saveTracklist
			},
			spinner: new wp.media.view.Spinner({
				priority: 100
			})
		});

		this.options.items.spinner.delay = 0;
		this.listenTo( state, 'change:isDirty', this.toggleButtonState );
		this.listenTo( state.get( 'collection' ), 'add remove reset', this.toggleButtonState );

		wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
	},

	render: function() {
		this.$button = this.get( 'save' ).$el;
		this.toggleButtonState();
		return this;
	},

	addTrack: function() {
		var state = this.controller.state(),
			collection = state.get( 'collection' );

		collection.add({
			menu_order: collection.length,
			record_id: collection.recordId,
			status: 'publish'
		});

		state.get( 'selection' ).reset( collection.last() );
	},

	saveTracklist: function() {
		var controller = this.controller,
			state = controller.state(),
			spinner = this.get( 'spinner' ).show();

		// Delete removed tracks.
		_.invoke( state.get( 'deleted' ).toArray(), 'destroy' );

		// Create or update tracks.
		state.get( 'collection' ).save().done(function() {
			spinner.hide();
			controller.close();
		});
	},

	toggleButtonState: function() {
		var state = this.controller.state(),
			isDirty = state.get( 'isDirty' ),
			collection = state.get( 'collection' );

		this.$button.attr( 'disabled', ! isDirty || collection.length < 1 );
	}
});

module.exports = TracklistToolbar;
