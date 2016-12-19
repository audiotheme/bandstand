module.exports = function( grunt, options ) {
	return {
		'default': [
			'build',
			'watch'
		],
		'build': [
			'check',
			'build:css',
			'build:js'
		],
		'build:css': [
			'sass',
			'postcss',
			'cssmin'
		],
		'build:js': [
			'browserify:build',
			'uglify'
		],
		'develop:js': [
			'browserify:develop'
		],
		'check': [
			'jshint',
			'jscs'
		],
		'package': [
			'check',
			'string-replace:package',
			'build:css',
			'build:js',
			'compress:package'
		]
	};
};
