/**
 * @file
 * Script to clone & rename starterkit.
 */

'use strict';

var gulp = require('gulp'),
replace = require('gulp-replace'),
rename = require('gulp-regex-rename'),
argv = require('yargs').argv,
sourceFiles = [ 'STARTERKIT/*',  'STARTERKIT/.*', 'STARTERKIT/*/**'],
del = require('del'),
theme_name = (argv.theme_name === undefined) ? false : argv.theme_name,
destination = '../' + theme_name;
if (!theme_name) {
  return console.log('You shoud specify theme name `gulp --theme_name new_theme_name`');
}

gulp.task('default', ['clean'], function () {
  if (!theme_name) {
    return console.log('You shoud specify theme name `gulp --theme_name new_theme_name`');
  }
  return gulp
    .src(sourceFiles)
    .pipe(replace('STARTERKIT', theme_name))
    .pipe(rename(/STARTERKIT/, theme_name))
    .pipe(gulp.dest(destination));
});

// Clean files.
gulp.task('clean', function () {
  if (!theme_name) {
    return console.log('You shoud specify theme name `gulp --theme_name new_theme_name`');
  }
  return del([ destination + '/*' ], {force: true});
});
