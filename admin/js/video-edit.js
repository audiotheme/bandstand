/* jshint newcap: false */
/* globals _, _bandstandVideoThumbnailPing:true, Backbone, jQuery, wp, WPSetThumbnailHTML, WPSetThumbnailID */

(function( window, $, _, Backbone, wp, undefined ) {
	'use strict';

	var theVideo,
		app = {};

	_.extend( app, { model: {}, view: {} } );

	window._bandstandVideoThumbnailPing = function() {
		var data = JSON.parse( $( '#postimagediv' ).find( '#bandstand-video-thumbnail-data' ).html() );

		if ( theVideo ) {
			theVideo.set( 'thumbnailId', data.thumbnailId );
			theVideo.set( 'oembedThumbnailId', data.oembedThumbnailId );
		}
	};

	app.model.Video = Backbone.Model.extend({
		defaults: {
			title: '',
			videoUrl: '',
			thumbnailId: '',
			oembedThumbnailId: ''
		},

		getEmbedHtml: function() {
			return wp.ajax.post( 'parse-embed', {
				post_ID: this.get( 'id' ),
				shortcode: '[embed]' + this.get( 'videoUrl' ) + '[/embed]'
			});
		},

		getRemoteThumbnail: function() {
			return wp.ajax.post( 'bandstand_get_video_oembed_data', {
				post_id: this.get( 'id' ),
				video_url: this.get( 'videoUrl' )
			});
		}
	});

	app.view.PostForm = wp.Backbone.View.extend({
		el: '#post',

		events: {
			'input #bandstand-video-url': 'updateVideoUrl'
		},

		initialize: function() {
			this.render();
		},

		render: function() {
			new app.view.MetaBoxThumbnail({
				model: this.model
			});

			this.views.add( '.bandstand-edit-after-title', [
				new app.view.Preview({
					model: this.model
				})
			]);

			return this;
		},

		updateVideoUrl: function() {
			this.model.set( 'videoUrl', this.$el.find( '#bandstand-video-url' ).val() );
		}
	});

	app.view.Preview = wp.Backbone.View.extend({
		tagName: 'div',
		className: 'bandstand-video-preview',

		initialize: function() {
			this.listenTo( this.model, 'change:videoUrl', this.render );
		},

		render: function() {
			var self = this;

			// Don't continue if the video URL is empty.
			if ( '' === this.model.get( 'videoUrl' ) ) {
				self.$el.hide().empty();
				return this;
			}

			this.model.getEmbedHtml().done(function( response ) {
				self.$el.html( response.body ).show();

				if ( $.isFunction( $.fn.fitVids ) ) {
					self.$el.fitVids();
				}
			}).fail(function() {
				self.$el.hide().empty();
			});

			return this;
		}
	});

	app.view.MetaBoxThumbnail = wp.Backbone.View.extend({
		el: '#postimagediv',

		events: {
			'click #bandstand-select-oembed-thumb-button': 'getRemoteThumbnail'
		},

		initialize: function() {
			this.toggleLink();
			this.listenTo( this.model, 'change:videoUrl change:thumbnailId change:oembedThumbnailId', this.toggleLink );
		},

		getRemoteThumbnail: function( e ) {
			var $spinner = this.$el.find( '#bandstand-select-oembed-thumb .spinner' ).css({
				display: 'inline-block',
				visibility: 'visible'
			});

			e.preventDefault();

			this.model.getRemoteThumbnail()
				.always(function() {
					$spinner.hide();
				})
				.done(function( response ) {
					WPSetThumbnailID( response.thumbnailId );
					WPSetThumbnailHTML( response.thumbnailMetaBoxHtml );
				});
		},

		toggleLink: function() {
			var $el = $( '#bandstand-select-oembed-thumb' ),
				thumbnailId = this.model.get( 'thumbnailId' ),
				oembedThumbnailId = this.model.get( 'oembedThumbnailId' );

			if (
				'' === this.model.get( 'videoUrl' ) ||
				( '' !== thumbnailId && thumbnailId === oembedThumbnailId )
			) {
				$el.hide();
			} else {
				$el.show();
			}
		}
	});

	$( document ).ready(function() {
		theVideo = new app.model.Video({
			id: parseInt( $( '#post_ID' ).val(), 10 ),
			title: $( '#title' ).val(),
			videoUrl: $( '#bandstand-video-url' ).val()
		});

		new app.view.PostForm({
			model: theVideo
		});

		_bandstandVideoThumbnailPing();
	});

})( window, jQuery, _, Backbone, wp );
