
module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    less: {
      appStyles: {
        src: 'assets/css/app-styles.less',
        dest: 'assets/css/styles.css'
      }
    }
  });
  grunt.loadNpmTasks('grunt-contrib-less');
  return grunt.registerTask('default', ['less']);
};
