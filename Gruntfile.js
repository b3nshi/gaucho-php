module.exports = function(grunt) {
  // var target = grunt.option('env') || 'local',
  // confData = grunt.file.readJSON('env/' + target + '.conf');

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      options: {
        livereload: true,
      },
      files:['gaucho/**/*', 'index.php'],
      tasks: ''
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['watch']);
};
