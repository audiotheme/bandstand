/* jshint browserify: true */
/* globals pluploadL10n */

var TracklistFrame,
	_ = require( 'underscore' ),
	l10n = require( 'bandstand' ).l10n,
	RecordFinderController = require( '../../controllers/record-finder' ),
	RecordFinder = require( '../record-finder' ),
	RecordFinderToolbar = require( '../toolbar/record-finder' ),
	TracklistController = require( '../../controllers/tracklist' ),
	TracklistManager = require( '../tracklist-manager' ),
	TracklistToolbar = require( '../toolbar/tracklist' ),
	wp = require( 'wp' );

TracklistFrame = wp.media.view.MediaFrame.Select.extend({
	className: 'media-frame bandstand-tracklist-frame',

	initialize: function() {
		_.defaults( this.options, {
			autoNumberTracklist: false,
			collection: null,
			modal: true,
			services: null,
			state: 'tracklist',
			title: ''
		});

		wp.media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
	},

	createStates: function() {
		this.states.add([
			new TracklistController({
				autoNumber: this.options.autoNumberTracklist,
				collection: this.collection
			}),
			new wp.media.controller.Library({
				title: l10n.importFromMediaLibrary || 'Import from Media Library',
				menu: 'default',
				menuItem: {
					text: l10n.importFromMedia || 'Import from Media',
					priority: 120
				},
				toolbar: 'media-import',
				allowLocalEdits: true,
				editable: false,
				displaySettings: false,
				displayUserSettings: false,
				filterable: 'uploaded',
				library: wp.media.query({ type: 'audio' }),
				multiple: 'add'
			}),
			new RecordFinderController({
				collection: this.collection,
				frame: this,
				query: this.options.record.title,
				services: this.options.services
			}),
			new wp.media.controller.Library({
				id: 'select',
				title: l10n.selectFile || 'Select File',
				button: {
					close: false
				},
				library: wp.media.query({ type: 'audio' }),
				menu: 'select',
				multiple: false,
				priority: 140,
				displaySettings: false,
				displayUserSettings: false,
				filterable: 'uploaded'
			})
		]);
	},

	bindHandlers: function() {
		_.bindAll( this, 'addUploadedAttachmentAsTrack' );

		this.on( 'menu:create:default', this.createMenu, this );
		this.on( 'content:create:bandstand-tracklist', this.createContent, this );
		this.on( 'toolbar:create:tracklist', this.createToolbar, this );

		this.on( 'toolbar:create:media-import', wp.media.view.MediaFrame.Select.prototype.createToolbar, this );
		this.on( 'toolbar:render:media-import', this.renderMediaImportToolbar, this );

		this.on( 'content:create:record-finder', this.createRecordFinderContent, this );
		this.on( 'toolbar:create:record-finder', this.createRecordFinderToolbar, this );

		this.on( 'menu:create:select', this.createSelectMenu, this );
		this.on( 'toolbar:render:select', this.renderSelectToolbar, this );

		this.on( 'uploader:ready', function() {
			this.uploader.uploader.uploader.bind( 'FileUploaded', this.addUploadedAttachmentAsTrack );
		}, this );

		wp.media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );
	},

	createContent: function( contentRegion ) {
		contentRegion.view = new TracklistManager({
			controller: this,
			collection: this.state().get( 'collection' ),
			selection: this.state().get( 'selection' )
		});
	},

	createRecordFinderContent: function( contentRegion ) {
		contentRegion.view = new RecordFinder({
			controller: this
		});
	},

	createMenu: function( menu ) {
		menu.view = new wp.media.view.Menu({
			controller: this
		});

		menu.view.set({
			'library-separator': new wp.media.View({
				className: 'separator',
				priority: 100
			})
		});
	},

	createToolbar: function( toolbar ) {
		toolbar.view = new TracklistToolbar({
			controller: this
		});
	},

	createRecordFinderToolbar: function( toolbar ) {
		toolbar.view = new RecordFinderToolbar({
			controller: this
		});
	},

	createSelectMenu: function( menu ) {
		var controller = this;

		menu.view = new wp.media.view.Menu({
			controller: this
		});

		menu.view.set({
			cancel: {
				text: l10n.cancel || 'Cancel',
				priority: 20,
				click: function() {
					controller.setState( 'tracklist' );
				}
			},
			separateCancel: new wp.media.View({
				className: 'separator',
				priority: 40
			})
		});
	},

	renderMediaImportToolbar: function( view ) {
		var controller = this;

		this.selectionStatusToolbar( view );

		view.set( 'select', {
			style: 'primary',
			priority: 80,
			text: l10n.select || 'Select',
			requires: { selection: true },
			click: function() {
				var collection = controller.state( 'tracklist' ).get( 'collection' ),
					selection = controller.state( 'library' ).get( 'selection' );

				selection.each(function( attachment ) {
					collection.add({
						artist: attachment.get( 'meta' ).artist || '',
						duration: attachment.get( 'fileLength' ),
						menu_order: collection.length,
						record_id: collection.recordId,
						status: 'publish',
						stream_url: attachment.get( 'url' ),
						title: attachment.get( 'title' )
					});
				});

				selection.reset();

				controller.setState( 'tracklist' );
			}
		});
	},

	renderSelectToolbar: function( view ) {
		var controller = this;

		view.set( 'select', {
			style: 'primary',
			priority: 80,
			text: l10n.select || 'Select',
			requires: { selection: true },
			click: function() {
				var model = controller.state().get( 'model' ),
					selection = controller.state().get( 'selection' ),
					setting = controller.state().get( 'setting' );

				model.set( setting, selection.first().get( 'url' ) );
				selection.reset();
				controller.setState( 'tracklist' );
			}
		});
	},

	selectionStatusToolbar: function( view ) {
		view.set( 'selection', new wp.media.view.Selection({
			controller: this,
			collection: this.state( 'library' ).get( 'selection' ),
			priority:   -40
		}) );
	},

	addUploadedAttachmentAsTrack: function( up, file, response ) {
		var collection = this.state( 'tracklist' ).get( 'collection' );

		if ( 'tracklist' !== this.state().id ) {
			return;
		}

		try {
			response = JSON.parse( response.response );
		} catch ( ex ) {
			return up.error( pluploadL10n.default_error, ex, file );
		}

		collection.add({
			artist: response.data.meta.artist || '',
			duration: response.data.fileLength,
			menu_order: collection.length,
			record_id: collection.recordId,
			status: 'publish',
			stream_url: response.data.url,
			title: response.data.title
		});
	}
});

module.exports = TracklistFrame;
