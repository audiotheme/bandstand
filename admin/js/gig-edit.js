/* jshint browserify: true */
/* globals _bandstandGigEditSettings, _bandstandVenueManagerSettings, _pikadayL10n, isRtl, Pikaday */

var datepicker, frame, settings, wpScreen,
	$ = require( 'jquery' ),
	app = require( 'bandstand' ),
	Backbone = require( 'backbone' ),
	$time = $( '#gig-time' ),
	ss = sessionStorage || {},
	lastGigTime = 'lastGigTime' in ss ? new Date( ss.lastGigTime ) : null,
	$venueIdField = $( '#gig-venue-id' );

var GigVenueMetaBox = require( './gigs/views/meta-box/gig-venue' ),
	Venue = require( './gigs/models/venue' ),
	VenueFrame = require( './gigs/views/frame/venue' );

settings = app.settings( _bandstandGigEditSettings );
settings = app.settings( _bandstandVenueManagerSettings );

// Initialize the time picker.
$time.timepicker({
	'scrollDefaultTime': lastGigTime || '',
	'timeFormat': settings.timeFormat,
	'className': 'ui-autocomplete'
}).on( 'showTimepicker', function() {
	$( this ).addClass( 'open' );
	$( '.ui-timepicker-list' ).width( $( this ).outerWidth() );
}) .on( 'hideTimepicker', function() {
	$( this ).removeClass( 'open' );
}) .next().on( 'click', function() {
	$time.focus();
});

// Add the last saved date and time to session storage
// when the gig is saved.
$( '#publish' ).on( 'click', function() {
	var time = $time.timepicker( 'getTime' );

	if ( ss && '' !== time ) {
		ss.lastGigTime = time;
	}
});

// Initialize the date picker.
datepicker = new Pikaday({
	bound: false,
	container: document.getElementById( 'bandstand-gig-start-date-picker' ),
	field: $( '.bandstand-gig-date-picker-start' ).find( 'input' ).get( 0 ),
	format: 'YYYY/MM/DD',
	i18n: _pikadayL10n || {},
	isRTL: isRtl,
	theme: 'bandstand-pikaday'
});

// Initialize the venue frame.
frame = new VenueFrame({
	title: app.l10n.venues || 'Venues',
	button: {
		text: app.l10n.selectVenue || 'Select Venue'
	}
});

// Refresh venue in case data was edited in the modal.
frame.on( 'close', function() {
	var venue = wpScreen.get( 'venue' );

	if ( venue.get( 'id' ) ) {
		venue.fetch();
	}
});

frame.on( 'insert', function( selection ) {
	wpScreen.set( 'venue', selection.first() );
	$venueIdField.val( selection.first().get( 'id' ) );
});

wpScreen = new Backbone.Model({
	frame: frame,
	venue: new Venue( settings.venue || {} )
});

new GigVenueMetaBox({
	controller: wpScreen
}).render();

$( window ).on( 'keyup', function( e ) {
	// Only handle key events when the venue list state is active.
	if ( ! frame.$el.is( ':visible' ) || 'venues' !== frame.state().id ) {
		return;
	}

	// Up arrow.
	if ( 38 === e.keyCode ) {
		frame.state().previous();
	}

	// Down arrow.
	if ( 40 === e.keyCode ) {
		frame.state().next();
	}
});

settings = app.settings( _bandstandGigEditSettings );
settings = app.settings( _bandstandVenueManagerSettings );
