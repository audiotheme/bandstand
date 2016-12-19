module.exports = {
	options: {
		config: 'config/.jscsrc'
	},
	check: {
		files: {
			src: [
				'{admin,includes}/js/{,**/}*.js',
				'!{admin,includes}/js/*.{bundle,min}.js',
				'!includes/js/vendor/*.js'
			]
		}
	}
};
