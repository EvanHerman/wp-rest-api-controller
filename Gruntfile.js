'use strict';
module.exports = function(grunt) {

  grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		// js minification
    uglify: {
      dist: {
        files: {
          // admin scripts
          'admin/js/min/rest-api-exposed-admin.min.js': [ // all other admin scripts
            'admin/js/rest-api-exposed-admin.js',
          ],
					// public scripts
          'public/js/min/rest-api-exposed-public.min.js': [ // all other admin scripts
            'public/js/rest-api-exposed-public.js',
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
        src: ['admin/css/*.css', 'public/css/*.css']
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
						'admin/css/min/rest-api-exposed-admin.min.css':
						[
							'admin/css/rest-api-exposed-admin.css',
						],
          },
					// public css files
          {
						'public/css/min/rest-api-exposed-public.min.css':
						[
							'public/css/rest-api-exposed-public.css',
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
						'admin/css/min/rest-api-exposed-admin.min.css',
						'admin/js/min/rest-api-exposed-admin.min.js',
						'public/css/min/rest-api-exposed-public.min.css',
						'public/js/min/rest-api-exposed-public.min.js',
					]
	      }
	    }
	  },

    // watch our project for changes
    watch: {
      public_css: { // public css
        files: 'public/css/*.css',
        tasks: ['cssmin', 'usebanner'],
        options: {
          spawn: false,
          event: ['all']
        },
      },
			admin_css: { // admin css
        files: 'admin/css/*.css',
        tasks: ['cssmin', 'usebanner'],
        options: {
          spawn: false,
          event: ['all']
        },
      },
			public_js: { // public js
			 files: 'public/js/*.js',
			 tasks: ['uglify', 'usebanner'],
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

  });

  // load tasks
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-banner');
  grunt.loadNpmTasks('grunt-postcss'); // CSS autoprefixer plugin (cross-browser auto pre-fixes)
  grunt.loadNpmTasks('grunt-auto-install'); // autoload all of ourd ependencies (ideally, you install this one package, and run grunt auto_install to install our dependencies automagically)

  // register task
  grunt.registerTask('default', [
		'uglify',
    'postcss',
    'cssmin',
		'usebanner',
    'watch',
  ]);

};
