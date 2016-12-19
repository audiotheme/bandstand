var config,
	_ = require( 'lodash' );

config = {
	options: {
		alias: {
			bandstand: './includes/js/modules/application.js'
		},
		plugin: [
			[
				'remapify', [
					{
						src: '*.js',
						expose: 'collections',
						cwd: './includes/js/collections'
					},
					{
						src: '*.js',
						expose: 'models',
						cwd: './includes/js/models'
					}
				]
			]
		],
		watch: true
	},
	build: {
		options: {},
		files: [
			{
				src: 'admin/js/admin.js',
				dest: 'admin/js/admin.bundle.js'
			},
			{
				src: 'admin/js/gig-edit.js',
				dest: 'admin/js/gig-edit.bundle.min.js'
			},
			{
				src: 'admin/js/record-edit.js',
				dest: 'admin/js/record-edit.bundle.min.js'
			},
			{
				src: 'admin/js/venue-edit.js',
				dest: 'admin/js/venue-edit.bundle.min.js'
			},
			{
				src: 'includes/js/bandstand.js',
				dest: 'includes/js/bandstand.bundle.js'
			},
			{
				src: 'includes/js/tracklists.js',
				dest: 'includes/js/tracklists.bundle.js'
			}
		]
	}
};

config.develop = _.cloneDeep(config.build);
config.develop.options.keepAlive = true;

module.exports = config;
