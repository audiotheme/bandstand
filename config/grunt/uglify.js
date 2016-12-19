module.exports = {
	build: {
		files: [
			{
				src: 'admin/js/admin.bundle.js',
				dest: 'admin/js/admin.bundle.min.js'
			},
			{
				src: 'admin/js/media.js',
				dest: 'admin/js/media.min.js'
			},
			{
				src: 'admin/js/gig-edit.bundle.min.js',
				dest: 'admin/js/gig-edit.bundle.min.js'
			},
			{
				src: 'admin/js/record-edit.bundle.min.js',
				dest: 'admin/js/record-edit.bundle.min.js'
			},
			{
				src: 'admin/js/venue-edit.bundle.min.js',
				dest: 'admin/js/venue-edit.bundle.min.js'
			},
			{
				src: 'includes/js/bandstand.bundle.js',
				dest: 'includes/js/bandstand.bundle.min.js'
			}
		]
	}
};
