module.exports = {
	build: {
		files: [
			{
				src: 'includes/scss/bandstand.scss',
				dest: 'includes/css/bandstand.min.css'
			},
			{
				src: 'admin/scss/admin.scss',
				dest: 'admin/css/admin.min.css'
			},
			{
				src: 'admin/scss/dashboard.scss',
				dest: 'admin/css/dashboard.min.css'
			},
			{
				src: 'admin/scss/venue-manager.scss',
				dest: 'admin/css/venue-manager.min.css'
			}
		]
	}
};
