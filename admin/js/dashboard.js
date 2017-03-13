/* globals _, _bandstandDashboardSettings, Backbone, jQuery, wp */

(function( window, $, _, Backbone, wp, undefined ) {
	'use strict';

	var app = {},
		settings = _bandstandDashboardSettings,
		l10n = settings.l10n;

	delete settings.l10n;

	_.extend( app, { controller: {}, model: {}, view: {} } );

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */

	app.model.Module = Backbone.Model.extend({
		defaults: {
			id: '',
			name: '',
			description: '',
			adminMenuId: '',
			canToggle: false,
			isActive: true,
			nonces: []
		},

		toggleStatus: function() {
			var module = this;

			return wp.ajax.post( 'bandstand_ajax_toggle_module', {
				module: this.get( 'id' ),
				nonce: this.get( 'nonces' ).toggle
			}).done(function( response ) {
				module.set( response ).toggleAdminMenu();
			}).fail(function() {

			});
		},

		toggleAdminMenu: function() {
			var selector = this.get( 'adminMenuId' );
			$( '#' + selector + ', .wp-submenu > .' + selector ).toggle( this.get( 'isActive' ) );
		}
	});

	app.model.Modules = Backbone.Collection.extend({
		model: app.model.Module
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	app.view.ModuleCard = wp.Backbone.View.extend({
		initialize: function( options ) {
			this.model = options.model;

			this.listenTo( this.model, 'change:isActive', this.updateStatusClass );
		},

		render: function() {
			this.toggleView = new app.view.ToggleSwitch({
				model: this.model
			});

			this.$el.find( '.bandstand-module-card-header' )
				.append( this.toggleView.render().$el );

			this.buttonView = new app.view.ToggleButton({
				model: this.model
			});

			this.$el.find( '.bandstand-module-card-body' )
				.append( this.buttonView.render().$el );

			this.updateStatusClass();
			return this;
		},

		updateStatusClass: function() {
			var isActive = this.model.get( 'isActive' );
			this.$el.toggleClass( 'is-active', isActive ).toggleClass( 'is-inactive', ! isActive );
		}
	});

	app.view.ToggleButton = wp.Backbone.View.extend({
		className: 'button button-primary button-activate',
		tagName: 'button',

		events: {
			'click': 'toggleStatus'
		},

		initialize: function( options ) {
			this.model = options.model;

			this.listenTo( this.model, 'change:isActive', this.updateStatus );
		},

		render: function() {
			this.$spinner = $( '<span class="spinner" />' );
			this.updateStatus();
			return this;
		},

		toggleStatus: function( e ) {
			var view = this;

			e.preventDefault();

			view.$el.attr( 'disabled', true );
			view.$spinner.insertBefore( view.$el ).addClass( 'is-active' );

			this.model.toggleStatus().done(function() {
				view.$el.attr( 'disabled', false );
				view.$spinner.removeClass( 'is-active' );
			});
		},

		updateStatus: function() {
			var isActive = this.model.get( 'isActive' ),
				text = isActive ? l10n.deactivate : l10n.activate;

			this.$el.text( text ).toggleClass( 'button-primary', ! isActive );

			if ( ! this.model.get( 'canToggle' ) ) {
				this.$el.hide();
			} else {
				this.$el.css( 'display', '' );
			}
		}
	});

	app.view.ToggleSwitch = wp.Backbone.View.extend({
		className: 'bandstand-toggle-switch',
		template: wp.template( 'bandstand-module-toggle-switch' ),

		events: {
			'click': 'toggleStatus'
		},

		initialize: function( options ) {
			this.model = options.model;
			this.listenTo( this.model, 'change:isActive', this.updateStatus );
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.$checkbox = this.$( 'input[type="checkbox"]' );
			this.$label = this.$( 'label' ).find( 'span' );
			this.updateStatus();
			return this;
		},

		toggleStatus: function( e ) {
			e.preventDefault();
			this.model.toggleStatus();
		},

		updateStatus: function() {
			var isActive = this.model.get( 'isActive' ),
				text = isActive ? l10n.deactivate : l10n.activate;

			this.$checkbox.prop( 'checked', isActive );
			this.$label.text( text );

			if ( ! this.model.get( 'canToggle' ) ) {
				this.$el.hide();
			} else {
				this.$el.css( 'display', '' );
			}
		}
	});

	/**
	 * ========================================================================
	 * SETUP
	 * ========================================================================
	 */

	$( document ).ready(function() {
		var modules = new app.model.Modules( _.where( settings.modules, { showInDashboard: true }) );

		$( '.bandstand-module-card' ).each(function() {
			var cardView,
				$module = $( this ),
				model = modules.get( $module.data( 'module-id' ) );

			model.set({
				description: $module.find( '.bandstand-module-card-description' ).text()
			});

			cardView = new app.view.ModuleCard({
				el: this,
				model: model
			});

			cardView.render();
		});
	});

})( window, jQuery, _, Backbone, wp );
