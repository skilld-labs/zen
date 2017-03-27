/**
 * @file
 * Script to build the theme.
 */

/* eslint-env node, es6 */
/* global Promise */
/* eslint-disable key-spacing, one-var, no-multi-spaces, max-nested-callbacks, quote-props */
/* eslint strict: ["error", "global"] */

'use strict';

let importOnce = require('node-sass-import-once'),
  path = require('path'),
  glob = require('glob'),
  env = process.env.NODE_ENV || 'testing',
  isProduction = (env === 'production');

let options = {};

// Edit these paths and options.
// The root paths are used to construct all the other paths in this
// configuration. The "project" root path is where this gulpfile.js is located.
// While Zen distributes this in the theme root folder, you can also put this
// (and the package.json) in your project's root folder and edit the paths
// accordingly.
options.rootPath = {
  project : __dirname + '/',
  styleGuide : __dirname + '/styleguide/',
  src : __dirname + '/src/',
  dist : __dirname + '/dist/',
  theme : __dirname + '/'
};

options.theme = {
  name : 'STARTERKIT',
  root : options.rootPath.theme,
  components : options.rootPath.src + 'sass/',
  build : options.rootPath.dist,
  css : options.rootPath.dist + 'css/',
  js : options.rootPath.src + 'js/',
  node : options.rootPath.theme + 'node_modules/',
  images     : options.rootPath.theme + 'images/',
  sprites    : options.rootPath.src + 'sprites/*'
};

// Set the URL used to access the Drupal website under development. This will
// allow Browser Sync to serve the website and update CSS changes on the fly.
options.drupalURL = '';

// Converts module names to absolute paths for easy imports.
function sassModuleImporter(url, file, done) {
  try {
    let pathResolution = require.resolve(url);
    return done({
      file: pathResolution
    });
  }
  catch (e) {
    return null;
  }
}

// Define the node-sass configuration. The includePaths is critical!
options.sass = {
  importer: [sassModuleImporter, importOnce],
  includePaths: [options.theme.components, options.theme.node],
  outputStyle: (isProduction ? 'compresssed' : 'expanded')
};

// Define which browsers to add vendor prefixes for.
options.autoprefixer = {
  browsers: [
    '> 1%',
    'ie 9'
  ]
};

// Help KSS to automatically find new component CSS files.
let cssFiles = glob.sync('*.css', {cwd: options.theme.css}),
  cssStyleguide = [];

cssFiles.forEach(function (file) {
  file = path.relative(options.rootPath.styleGuide, options.theme.css) + '/' + file;
  cssStyleguide.push(file);
});

// Define the style guide paths and options.
options.styleGuide = {
  source: [
    options.theme.components
  ],
  mask: /\.less|\.sass|\.scss|\.styl|\.stylus/,
  destination: options.rootPath.styleGuide,

  builder: 'builder/twig',
  namespace: options.theme.name + ':' + options.theme.components,
  'extend-drupal8': true,

  // The css and js paths are URLs, like '/misc/jquery.js'.
  // The following paths are relative to the generated style guide.
  css: cssStyleguide,
  js: [
  ],

  homepage: 'homepage.md',
  title: 'STARTERKIT Style Guide'
};

// Define the paths to the JS files to lint.
options.eslint = {
  files : [
    options.rootPath.project + 'gulpfile.js',
    options.theme.js + '**/*.js',
    '!' + options.theme.js + '**/*.min.js',
    options.theme.components + '**/*.js',
    '!' + options.theme.build + '**/*.js'
  ]
};

// Define the paths for gulp.spritesmith
options.sprites = {
  imgName: options.rootPath.dist + 'sprites/sprites.png',
  cssName: 'src/sass/abstractions/_sprites.scss',
  imgPath: path.relative(options.theme.css, options.theme.images + 'sprites/sprites.png'),
  cssVarMap: function (sprite) {
    sprite.name = 'sprite_' + sprite.name;
  }
};

// If your files are on a network share, you may want to turn on polling for
// Gulp watch. Since polling is less efficient, we disable polling by default.
// Use `options.gulpWatchOptions = {interval: 1000, mode: 'poll'};` as example.
options.gulpWatchOptions = {};

// Load Gulp and tools we will use.
// Task gulp-load-plugins will report "undefined" error unless you load
// gulp-sass manually.
let gulp = require('gulp'),
  $ = require('gulp-load-plugins')(),
  browserSync = require('browser-sync').create(),
  del = require('del'),
  sass = require('gulp-sass'),
  kss = require('kss'),
  cache = require('gulp-cached'),
  spritesmith = require('gulp.spritesmith'),
  webpack       = require('webpack'),
  webpackStream = require('webpack-stream');

// The default task.
gulp.task('default', ['build']);

// Build everything.
gulp.task('build', ['sprites', 'scripts', 'styles', 'lint']);

// Build Sprites.
gulp.task('sprites', function () {
  let spriteData = gulp.src(options.theme.sprites).pipe(spritesmith(options.sprites));
  return spriteData.pipe(gulp.dest('.'));
});

// #################
// Build Javascript.
// #################
gulp.task('scripts', [], function () {
  return gulp.src(options.theme.js + 'init.js')
    .pipe(webpackStream(require('./webpack.config.js'), webpack))
    .pipe(gulp.dest(options.rootPath.dist));
});

// Build CSS.
let sassFiles = [
  options.theme.components + '**/*.scss',
  // Do not open Sass partials as they will be included as needed.
  '!' + options.theme.components + '**/_*.scss',
  // Chroma markup has its own gulp task.
  '!' + options.theme.components + 'style-guide/kss-example-chroma.scss'
];

gulp.task('styles', ['sprites', 'clean:css'], function () {
  return gulp.src(sassFiles)
    .pipe($.if(!isProduction, $.sourcemaps.init()))
    .pipe($.if(!isProduction, cache()))
    .pipe(sass(options.sass).on('error', sass.logError))
    .pipe($.autoprefixer(options.autoprefixer))
    .pipe($.rename({dirname: ''}))
    .pipe($.size({showFiles: true}))
    .pipe($.if(!isProduction,  $.sourcemaps.write('./')))
    .pipe(gulp.dest(options.theme.css))
    .pipe($.if(browserSync.active, browserSync.stream({match: '**/*.css'})));
});

// Build style guide.
gulp.task('styleguide', ['clean:styleguide', 'styleguide:kss-example-chroma'], function () {
  return kss(options.styleGuide);
});

gulp.task('styleguide:kss-example-chroma', function () {
  return gulp.src(options.theme.components + 'style-guide/kss-example-chroma.scss')
    .pipe(sass(options.sass).on('error', sass.logError))
    .pipe($.replace(/(\/\*|\*\/)\n/g, ''))
    .pipe($.rename('kss-example-chroma.twig'))
    .pipe($.size({showFiles: true}))
    .pipe(gulp.dest(options.rootPath.dist + 'twig'));
});

// Debug the generation of the style guide with the --verbose flag.
gulp.task('styleguide:debug', ['clean:styleguide', 'styleguide:kss-example-chroma'], function () {
  options.styleGuide.verbose = true;
  return kss(options.styleGuide);
});

// Lint Sass and JavaScript.
gulp.task('lint', ['lint:sass', 'lint:js']);

// Lint JavaScript.
gulp.task('lint:js', function () {
  return gulp.src(options.eslint.files)
    .pipe($.eslint())
    .pipe($.eslint.format())
    .pipe($.eslint.failOnError());
});

// Lint Sass.
gulp.task('lint:sass', function () {
  return gulp.src(options.theme.components + '**/*.scss')
    .pipe($.sassLint())
    .pipe($.sassLint.format())
    .pipe($.sassLint.failOnError());
});

// Watch for changes and rebuild.
gulp.task('watch', ['browser-sync', 'watch:lint-and-styleguide', 'watch:js']);

gulp.task('browser-sync', ['watch:css'], function () {
  if (!options.drupalURL) {
    return Promise.resolve();
  }
  return browserSync.init({
    proxy: options.drupalURL,
    noOpen: false
  });
});

gulp.task('watch:css', ['clean:css', 'styles'], function () {
  return gulp.watch(options.theme.components + '**/*.scss', options.gulpWatchOptions, ['styles']);
});

gulp.task('watch:lint-and-styleguide', ['styleguide', 'lint:sass'], function () {
  return gulp.watch([
    options.theme.components + '**/*.scss',
    options.theme.components + '**/*.twig'
  ], options.gulpWatchOptions, ['styleguide', 'lint:sass']);
});

gulp.task('watch:js', ['lint:js'], function () {
  return gulp.watch(options.eslint.files, options.gulpWatchOptions, ['lint:js']);
});

// Clean all directories.
gulp.task('clean', ['clean:css', 'clean:dist', 'clean:styleguide']);

// Clean style guide files.
gulp.task('clean:styleguide', function () {
  // You can use multiple globbing patterns as you would with `gulp.src`.
  return del([
    options.styleGuide.destination + '*.html',
    options.styleGuide.destination + 'kss-assets',
    options.theme.build + 'twig/*.twig'
  ], {force: true});
});

// Clean CSS files.
gulp.task('clean:css', function () {
  return del([
    options.theme.css + '**/*.css',
    options.theme.css + '**/*.map'
  ], {force: true});
});

// Clean `dist` folder.
gulp.task('clean:dist', function () {
  return del([
    options.rootPath.dist + '/**/*'
  ], {force: true});
});


// Resources used to create this gulpfile.js:
// - https://github.com/google/web-starter-kit/blob/master/gulpfile.babel.js
// - https://github.com/dlmanning/gulp-sass/blob/master/README.md
// - http://www.browsersync.io/docs/gulp/
