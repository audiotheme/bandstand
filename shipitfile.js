var project = require( __dirname + '/package.json' ),
	path = require( 'path' );

module.exports = function ( shipit ) {
	shipit.initConfig({
		staging: {
			servers: 'staging.cedaro.com',
			deployRoot: '/srv/www/dapperbeards.com/public/content/plugins/'
		}
	});

	shipit.task( 'deploy', function() {
		shipit.archiveFile = project.name + '-' + project.version + '.zip';
		shipit.deployPath = shipit.config.deployRoot;

		return createDeploymentPath()
			.then( copyProject() )
			.then( unpackDeployment );
	});

	function createDeploymentPath() {
		return shipit.remote( 'mkdir -p ' + shipit.deployPath );
	}

	function copyProject() {
		return shipit.remoteCopy( 'dist/' + shipit.archiveFile, shipit.deployPath );
	}

	function unpackDeployment() {
		var cmd = [];

		cmd.push( 'cd ' + shipit.deployPath );
		cmd.push( 'rm -rf ' + project.name );
		cmd.push( 'unzip -q ' + shipit.archiveFile );
		cmd.push( 'rm ' + shipit.archiveFile );

		return shipit.remote( cmd.join( ' && ' ) );
	}
};
