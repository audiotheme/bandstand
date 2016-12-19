module.exports = {
	options: {
		jshintrc: 'config/.jshintrc'
	},
	check: [
		'{admin,includes}/js/{,**/}*.js',
		'!{admin,includes}/js/*.{bundle,min}.js',
		'!includes/js/vendor/*.js'
	],
	grunt: {
		options: {
			jshintrc: 'config/.jshintnoderc'
		},
		src: [
			'Gruntfile.js',
			'config/grunt/*.js'
		]
	}
};
