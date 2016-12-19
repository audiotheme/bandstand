/* jshint browserify: true, -W079 */

module.exports = {
	displayRawTitle: function() {
		var title = this.title || '';
		return title.raw || title;
	}
};
