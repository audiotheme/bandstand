/* jshint browserify: true, -W079 */

'use strict';

module.exports = {
	/**
	 * Convert time in seconds to a human-readable time code.
	 *
	 * @param  {number} time Time in seconds.
	 * @return {string}      Human-readable time code in hh:mm:ss format.
	 */
	formatTime: function( time ) {
		var hours, minutes, seconds,
			timeCode = '';

		seconds = parseInt( time, 10 ) % 60;
		minutes = Math.floor( time / 60 ) % 60;
		hours   = Math.floor( time / 3600 );
		seconds = seconds < 10 ? '0' + seconds : seconds;

		if ( hours ) {
			minutes = minutes < 10 ? '0' + minutes : minutes;
			timeCode += hours + ':';
		}

		timeCode += minutes + ':' + seconds;

		return timeCode;
	}
};
