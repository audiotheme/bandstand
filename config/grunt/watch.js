module.exports = {
	sass: {
		files: [
			'{admin,includes}/scss/{,**/}*.scss'
		],
		tasks: [ 'sass', 'postcss', 'cssmin' ]
	}
};
