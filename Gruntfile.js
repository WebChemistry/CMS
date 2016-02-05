module.exports = function(grunt) {
    grunt.initConfig({
        useminPrepare: {
            html: [
                //'app/layouts/@admin.latte',
                //'app/modules/Homepage/Modules/Admin/templates/Homepage/default/login.latte',
                'app/layouts/@front.latte'
            ],
            options: {
                dest: '.'
            }
        },
        netteBasePath: {
            basePath: 'www',
            options: {
                removeFromPath: [
                    //'app\\modules\\Homepage\\Modules\\Admin\\templates\\Homepage\\default\\'
                    'app\\layouts\\'
                ]
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-usemin');
    grunt.loadNpmTasks('grunt-nette-basepath');
    return grunt.registerTask('default', ['useminPrepare', 'netteBasePath', 'concat', 'uglify', 'cssmin']);
};