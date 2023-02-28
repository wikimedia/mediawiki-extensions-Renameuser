/* eslint-env node, es6 */
module.exports = function ( grunt ) {
	grunt.loadNpmTasks( 'grunt-eslint' );

	grunt.initConfig( {
		eslint: {
			options: {
				cache: true
			},
			all: [
				'**/*.{js,json}',
				'!{vendor,node_modules}/**'
			]
		}
	} );

	grunt.registerTask( 'test', [ 'eslint' ] );
	grunt.registerTask( 'default', 'test' );
};
