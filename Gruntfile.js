/*jshint node:true */
module.exports = function(grunt) {
  grunt.loadNpmTasks("grunt-contrib-jshint");
  grunt.loadNpmTasks("grunt-jsonlint");
  grunt.loadNpmTasks("grunt-banana-checker");

  grunt.initConfig({
    jshint: {
      all: ["**/*.js", "!node_modules/**", "!vendor/**"]
    },
    banana: {
      all: "i18n/"
    },
    jsonlint: {
      all: ["**/*.json", "!node_modules/**", "!vendor/**"]
    }
  });

  grunt.registerTask("default", ["jshint", "jsonlint", "banana"]);
};
