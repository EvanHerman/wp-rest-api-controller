module.exports = function( grunt ) {
	'use strict';

	const pkg = grunt.file.readJSON( 'package.json' );

	grunt.initConfig( {

		pkg,

		replace: {
			php: {
				src: [
					'yikes-custom-taxonomy-order.php',
					'lib/**/*.php',
				],
				overwrite: true,
				replacements: [
					{
						from: /Version:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
						to: 'Version:$1' + pkg.version,
					},
					{
						from: /@since(.*?)NEXT/mg,
						to: '@since$1' + pkg.version,
					},
					{
						from: /Version:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
						to: 'Version:$1' + pkg.version,
					},
					{
						from: /define\(\s*'YIKES_STO_VERSION',\s*'(.*)'\s*\);/,
						to: 'define( \'YIKES_STO_VERSION\', \'<%= pkg.version %>\' );',
					},
					{
						from: /Tested up to:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
						to: 'Tested up to:$1' + pkg.tested_up_to,
					},
				],
			},
			readme: {
				src: 'readme.*',
				overwrite: true,
				replacements: [
					{
						from: /^(\*\*|)Stable tag:(\*\*|)(\s*?)[a-zA-Z0-9.-]+(\s*?)$/mi,
						to: '$1Stable tag:$2$3<%= pkg.version %>$4',
					},
					{
						from: /Tested up to:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
						to: 'Tested up to:$1' + pkg.tested_up_to,
					},
				],
			},
			tests: {
				src: '.dev/tests/phpunit/**/*.php',
				overwrite: true,
				replacements: [
					{
						from: /\'version\'(\s*?)\=\>(\s*?)\'(.*)\'/,
						to: '\'version\' \=\> \'<%= pkg.version %>\'',
					},
				],
			},
			languages: {
				src: 'languages/simple-taxonomy-ordering.pot',
				overwrite: true,
				replacements: [
					{
						from: /(Project-Id-Version: Simple Taxonomy Ordering )[0-9\.]+/,
						to: '$1' + pkg.version,
					},
				],
			},
		},

	} );

	grunt.loadNpmTasks( 'grunt-text-replace' );

	grunt.registerTask( 'version', [ 'replace' ] );
};
