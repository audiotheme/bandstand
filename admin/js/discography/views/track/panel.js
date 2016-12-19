/* jshint browserify: true */

var TrackPanel,
	$ = require( 'jquery' ),
	TrackEditForm = require( './edit-form' ),
	TrackSidebar = require( './sidebar' ),
	wp = require( 'wp' );

TrackPanel = wp.media.View.extend({
	tagName: 'div',
	className: 'bandstand-track-panel',

	events: {
		'change [data-setting]': 'updateAttribute',
		'click .js-select-attachment': 'selectMedia',
		'keyup [data-setting="title"]': 'updateAttribute',
		'keyup [data-setting="track_number"]': 'updateAttribute'
	},

	initialize: function( options ) {
		this.controller = options.controller;
		this.selection = options.selection;

		this.listenTo( this.selection, 'remove reset', this.render );
	},

	render: function() {
		this.$el.empty();
		this.model = this.selection.first();

		if ( ! this.selection.length ) {
			this.views.set([]);
		} else {
			this.views.set([
				new TrackEditForm({
					model: this.model
				}),
				new TrackSidebar({
					controller: this.controller,
					selection: this.selection
				})
			]);
		}

		return this;
	},

	selectMedia: function( e ) {
		e.preventDefault();

		this.controller.setState( 'select' );

		this.controller.state().set({
			model: this.model,
			setting: $( e.target ).closest( '.bandstand-input-group' ).find( '[data-setting]' ).data( 'setting' )
		});
	},

	/**
	 * Update a model attribute when a field is changed.
	 *
	 * Fields with a 'data-setting="{{key}}"' attribute whose value
	 * corresponds to a model attribute will be automatically synced.
	 *
	 * @param {Object} e Event object.
	 */
	updateAttribute: function( e ) {
		var $target = $( e.target ),
			attribute = $target.data( 'setting' ),
			value = e.target.value;

		// Use the correct value for checkboxes.
		if ( $target.is( 'input[type="checkbox"]' ) ) {
			value = $target[0].checked;
		}

		this.model.set( attribute, value );
	}
});

module.exports = TrackPanel;
