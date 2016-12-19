module.exports = {
	package: {
		options: {
			replacements: [
				{
					pattern: /(Version:[\s]+).+/,
					replacement: '$1<%= package.version %>'
				},
				{
					pattern: /@version .+/,
					replacement: '@version <%= package.version %>'
				},
				{
					pattern: /'BANDSTAND_VERSION', '[^']+'/,
					replacement: '\'BANDSTAND_VERSION\', \'<%= package.version %>\''
				}
			]
		},
		files: {
			'bandstand.php': 'bandstand.php',
			'style.css': 'style.css'
		}
	}
};
