'use strict';
module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		// js minification
		uglify: {
			dist: {
				files: {
					// admin scripts
					'admin/js/min/wp-rest-api-controller-admin.min.js': [ // all other admin scripts
						'admin/js/wp-rest-api-controller-admin.js',
					],
					// admin tooltip script (tipso.js)
					'admin/js/min/tipso.min.js': [ // all other admin scripts
						'admin/js/tipso.js',
					],
				}
			}
		},

		// Autoprefixer for our CSS files
		postcss: {
			options: {
				map: true,
				processors: [
					require('autoprefixer-core') ({
						browsers: ['last 2 versions']
					})
				]
			},
			dist: {
				src: ['admin/css/*.css']
			}
		},
		auto_install: {
			local: {}
		},

		// css minify all contents of our directory and add .min.css extension
		cssmin: {
			target: {
				files: [
					// admin css files
					{
						'admin/css/min/wp-rest-api-controller-admin.min.css':
						[
							'admin/css/wp-rest-api-controller-admin.css',
							'admin/css/tipso.css',
						],
					}
				]
			}
		},

		// Generate a nice banner for our css/js files
		usebanner: {
			taskName: {
				options: {
					position: 'top',
					replace: true,
					banner: '/*\n'+
						' * @Plugin <%= pkg.title %>\n' +
						' * @Author <%= pkg.author %>\n'+
						' * @Site <%= pkg.site %>\n'+
						' * @Version <%= pkg.version %>\n' +
						' * @Build <%= grunt.template.today("mm-dd-yyyy") %>\n'+
						' */',
					linebreak: true
				},
				files: {
					src: [
						'admin/css/min/wp-rest-api-controller-admin.min.css',
						'admin/js/min/wp-rest-api-controller-admin.min.js',
					]
				}
			}
		},

		// watch our project for changes
		watch: {
			admin_css: { // admin css
				files: 'admin/css/*.css',
				tasks: ['cssmin', 'usebanner'],
				options: {
					spawn: false,
					event: ['all']
				},
			},
		 admin_js: { // admin js
			files: 'admin/js/*.js',
			tasks: ['uglify', 'usebanner'],
			options: {
				spawn: false,
				event: ['all']
			},
		 },
		},

		pot: {
			options: {
				text_domain: 'wp-rest-api-controller',
				dest: 'languages/',
				keywords: [
					'__:1',
					'_e:1',
					'_x:1,2c',
					'esc_html__:1',
					'esc_html_e:1',
					'esc_html_x:1,2c',
					'esc_attr__:1',
					'esc_attr_e:1',
					'esc_attr_x:1,2c',
					'_ex:1,2c',
					'_n:1,2',
					'_nx:1,2,4c',
					'_n_noop:1,2',
					'_nx_noop:1,2,3c'
				],
			},
			files: {
				src:  [ '**/*.php' ],
				expand: true,
			}
		}

	});

	// load tasks
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-banner');
	grunt.loadNpmTasks('grunt-postcss'); // CSS autoprefixer plugin (cross-browser auto pre-fixes)
	grunt.loadNpmTasks('grunt-auto-install'); // autoload all of ourd ependencies (ideally, you install this one package, and run grunt auto_install to install our dependencies automagically)
	grunt.loadNpmTasks('grunt-pot');

	// register task
	grunt.registerTask('default', [
		'uglify',
		'postcss',
		'cssmin',
		'usebanner',
	]);

	// register task
	grunt.registerTask('watch', [
		'uglify',
		'postcss',
		'cssmin',
		'usebanner',
		'watch',
	]);

};
