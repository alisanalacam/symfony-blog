
module.exports = function (grunt) {

    var alias = require("grunt-browserify-alias");

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        bowercopy: {
            options: {
                srcPrefix: 'bower_components',
                destPrefix: 'web/assets'
            },
            scripts: {
                files: {
                    'js/jquery.js': 'jquery/dist/jquery.js',
                    'js/bootstrap.js': 'bootstrap/dist/js/bootstrap.js'
                }
            },
            stylesheets: {
                files: {
                    'css/bootstrap.css': 'bootstrap/dist/css/bootstrap.css'
                }
            },
            fonts: {
                files: {
                    'fonts': 'bootstrap/fonts'
                }
            }
        },
        browserify: {
            main: {
                src: ['web/assets/js/jquery.js', 'web/assets/js/bootstrap.js', 'web/assets/theme/js/main.js'],
                dest: 'web/assets/js/bundle.js'
            }
        }
    });
    grunt.loadNpmTasks('grunt-bowercopy');
    grunt.loadNpmTasks('grunt-browserify');

    grunt.registerTask('default', ['bowercopy', 'browserify']);

};