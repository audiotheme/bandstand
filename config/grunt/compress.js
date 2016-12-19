module.exports = {
	package: {
		options: {
			archive: 'dist/<%= package.name %>-<%= package.version %>.zip'
		},
		files: [
			{
				src: [
					'**',
					'!admin/scss/**',
					'!config/**',
					'!dist/**',
					'!includes/scss/**',
					'!node_modules/**',
					'!svn/**',
					'!tests/**',
					'!vendor/bin/**',
					'!vendor/composer/installers/**',
					'!vendor/composer/installed.json',
					'!.DS_Store',
					'!.editorconfig',
					'!.esformatter',
					'!.gitignore',
					'!composer.*',
					'!Gruntfile.js',
					'!package.json',
					'!phpcs.log',
					'!phpcs.xml',
					'!phpunit.xml',
					'!README.md',
					'!shipitfile.js'
				],
				dest: '<%= package.name %>/'
			}
		]
	}
};
