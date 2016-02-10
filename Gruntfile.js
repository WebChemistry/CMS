module.exports = function(grunt) {
    grunt.config.init({
        netteAssets: {
            composer: {
                taskName: 'cms',
                configJSON: 'app/config/composer-assets.json',
                basePath: 'www/'
            },
            target: {
                taskName: 'cms',
                config: 'app/config/assets.yaml',
                basePath: 'www/'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-nette-assets');


    return grunt.registerTask('default', ['netteAssets', 'uglify', 'cssmin']);
};