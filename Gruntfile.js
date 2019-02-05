/*jshint node:true */
module.exports = function(grunt) {
  grunt.loadNpmTasks("grunt-banana-checker");

  grunt.initConfig({
    banana: {
      all: "i18n/"
    }
  });

  grunt.registerTask("default", ["banana"]);
};
