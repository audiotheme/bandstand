/* jshint browserify: true */
/* globals _bandstandAdminSettings */

var methods,
	$ = require( 'jquery' ),
	app = require( 'bandstand' ),
	wp = require( 'wp' );

app.settings( _bandstandAdminSettings );

$(function( $ ) {
	$( '.wrap' ).on( 'focus', '.bandstand-input-group input', function() {
		$( this ).parent().addClass( 'is-focused' );
	}).on( 'blur', '.bandstand-input-group input', function() {
		$( this ).parent().removeClass( 'is-focused' );
	});
});

$(function( $ ) {
	$( '.bandstand-taxonomy-meta-box' ).each(function() {
		var $this = $( this ),
			$group = $this.find( '.bandstand-add-term-group' ),
			$button = $group.find( '.button' ).attr( 'disabled', false ),
			$field = $group.find( '.bandstand-add-term-field' ),
			$list = $this.find( '.bandstand-taxonomy-term-list ul' ),
			$response = $( '.bandstand-add-term-response' );

		$field.on( 'keypress', function( e ) {
			if ( 13 === e.which ) {
				e.preventDefault();
				$button.click();
			}
		});

		// Add a record type.
		$button.on( 'click', function() {
			$group.addClass( 'is-loading' );
			$button.attr( 'disabled', true );
			$response.text( '' ).hide();

			wp.ajax.post( 'bandstand_ajax_insert_term', {
				taxonomy: $this.data( 'taxonomy' ),
				term: $field.val(),
				nonce: $group.find( '.bandstand-add-term-nonce' ).val()
			}).done(function( response ) {
				$field.val( '' );
				$list.prepend( response.html );
			}).fail(function( response ) {
				$response.css( 'display', 'block' ).text( response.message );
			}).always(function() {
				$group.removeClass( 'is-loading' );
				$button.attr( 'disabled', false );
			});
		});
	});
});

/**
 * Repeater
 *
 * .bandstand-clear-on-add will clear the value of a form element in a newly added row.
 * .bandstand-hide-on-add will hide the element in a newly added row.
 * .bandstand-remove-on-add will remove an element from a newly added row.
 * .bandstand-show-on-add will show a hidden elment in a newly added row.
 */
methods = {
	init: function( options ) {
		var settings = {
			items: null
		};

		if ( options ) {
			$.extend( settings, options );
		}

		return this.each(function() {
			var repeater = $( this ),
				itemsParent = repeater.find( '.bandstand-repeater-items' ),
				itemTemplate, template;

			if ( repeater.data( 'item-template-id' ) ) {
				template = wp.template( repeater.data( 'item-template-id' ) );

				if ( settings.items ) {
					repeater.bandstandRepeater( 'clearList' );

					$.each( settings.items, function( i, item ) {
						itemsParent.append( template( item ).replace( /__i__/g, i ) );
					});
				}

				itemTemplate = template({});
				itemTemplate = $( itemTemplate.replace( /__i__/g, '0' ) );
			} else {
				itemTemplate = repeater.find( '.bandstand-repeater-item:eq(0)' ).clone();
			}

			repeater.data( 'itemIndex', repeater.find( '.bandstand-repeater-item' ).length || 0 );
			repeater.data( 'itemTemplate', itemTemplate );

			repeater.bandstandRepeater( 'updateIndex' );

			itemsParent.sortable({
				axis: 'y',
				forceHelperSize: true,
				forcePlaceholderSize: true,
				helper: function( e, ui ) {
					var $helper = ui.clone();
					$helper.children().each(function( index ) {
						$( this ).width( ui.children().eq( index ).width() );
					});

					return $helper;
				},
				update: function() {
					repeater.bandstandRepeater( 'updateIndex' );
				},
				change: function() {
					repeater.find( '.bandstand-repeater-sort-warning' ).fadeIn( 'slow' );
				}
			});

			repeater.find( '.bandstand-repeater-add-item' ).on( 'click', function( e ) {
				e.preventDefault();
				$( this ).closest( '.bandstand-repeater' ).bandstandRepeater( 'addItem' );
			});

			repeater.on( 'click', '.bandstand-repeater-remove-item', function( e ) {
				var repeater = $( this ).closest( '.bandstand-repeater' );
				e.preventDefault();
				$( this ).closest( '.bandstand-repeater-item' ).remove();
				repeater.bandstandRepeater( 'updateIndex' );
			});

			repeater.on( 'blur', 'input,select,textarea', function() {
				$( this ).closest( '.bandstand-repeater' ).find( '.bandstand-repeater-item' ).removeClass( 'bandstand-repeater-active-item' );
			}).on( 'focus', 'input,select,textarea', function() {
				$( this ).closest( '.bandstand-repeater-item' ).addClass( 'bandstand-repeater-active-item' ).siblings().removeClass( 'bandstand-repeater-active-item' );
			});
		});
	},

	addItem: function() {
		var repeater = $( this ),
			itemIndex = repeater.data( 'itemIndex' ),
			itemTemplate = repeater.data( 'itemTemplate' );

		repeater.bandstandRepeater( 'clearList' );

		repeater.find( '.bandstand-repeater-items' ).append( itemTemplate.clone() )
			.children( ':last-child' ).find( 'input,select,textarea' ).each(function() {
			var $this = $( this );
			$this.attr( 'name', $this.attr( 'name' ).replace( '[0]', '[' + itemIndex + ']' ) );
		}).end()
			.find( '.bandstand-clear-on-add' ).val( '' ).end()
			.find( '.bandstand-remove-on-add' ).remove().end()
			.find( '.bandstand-show-on-add' ).show().end()
			.find( '.bandstand-hide-on-add' ).hide().end();

		repeater.data( 'itemIndex', itemIndex + 1 ).bandstandRepeater( 'updateIndex' );

		repeater.trigger( 'addItem.bandstand', [ repeater.find( '.bandstand-repeater-items' ).children().last() ]);
	},

	clearList: function() {
		var itemsParent = $( this ).find( '.bandstand-repeater-items' );

		if ( itemsParent.hasClass( 'is-empty' ) ) {
			itemsParent.removeClass( 'is-empty' ).empty();
		}
	},

	updateIndex: function() {
		$( '.bandstand-repeater-index', this ).each(function( i ) {
			$( this ).text( i + 1 + '.' );
		});
	}
};

$.fn.bandstandRepeater = function( method ) {
	if ( methods[ method ] ) {
		return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
	} else if ( 'object' === typeof method || ! method ) {
		return methods.init.apply( this, arguments );
	} else {
		$.error( 'Method ' + method + ' does not exist on jQuery.bandstandRepeater' );
	}
};
