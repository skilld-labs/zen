/**
 * @file
 * Script to build the theme.
 */

/* eslint-env node, es6 */
/* global Promise */
/* eslint-disable key-spacing, one-var, no-multi-spaces, max-nested-callbacks, quote-props */
/* eslint strict: ["error", "global"] */

'use strict';

// Load Gulp and tools we will use.
// Task gulp-load-plugins will report "undefined" error unless you load
// gulp-sass manually.
// Pattern allows to lazy-load modules on demand.
var $ = require('gulp-load-plugins')({
    overridePattern: false,
    pattern: '*'
  }),
  browserSync = $.browserSync.create(),
  gulp = require('gulp'),

  env = process.env.NODE_ENV || 'testing',
  isProduction = (env === 'production');

var options = {};

// Edit these paths and options.
// The root paths are used to construct all the other paths in this
// configuration. The "project" root path is where this gulpfile.js is located.
// While Zen distributes this in the theme root folder, you can also put this
// (and the package.json) in your project's root folder and edit the paths
// accordingly.
options.rootPath = {
  project : __dirname + '/',
  styleGuide : __dirname + '/styleguide/',
  theme : __dirname + '/'
};

options.theme = {
  name : 'STARTERKIT',
  root : options.rootPath.theme,
  components : options.rootPath.theme + 'components/',
  build : options.rootPath.theme + 'components/asset-builds/',
  css : options.rootPath.theme + 'components/asset-builds/css/',
  js : options.rootPath.theme + 'js/',
  node : options.rootPath.theme + 'node_modules/',
  images     : options.rootPath.theme + 'images/',
  sprites    : options.rootPath.theme + 'images-source/*'
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
  importer: [sassModuleImporter, $.nodeSassImportOnce],
  includePaths: [options.theme.components, options.theme.node],
  outputStyle: (isProduction ? 'compresssed' : 'expanded'),
  errLogToConsole: true
};

// Define which browsers to add vendor prefixes for.
options.autoprefixer = {
  browsers: [
    '> 1%',
    'ie 9'
  ]
};

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
  css: [],
  js: [],

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
  imgName: options.theme.images + 'sprites/sprites.png',
  cssName: 'components/init/_sprites.scss',
  imgPath: $.path.relative(options.theme.css, options.theme.images + 'sprites/sprites.png'),
  cssVarMap: function (sprite) {
    sprite.name = 'sprite_' + sprite.name;
  }
};

// If your files are on a network share, you may want to turn on polling for
// Gulp watch. Since polling is less efficient, we disable polling by default.
// Use `options.gulpWatchOptions = {interval: 1000, mode: 'poll'};` as example.
options.gulpWatchOptions = {};

// Browsersync options
options.browserSync = {
  proxy: {
    target: options.drupalURL
  },
  xip: true
};

// The default task.
gulp.task('default', ['build']);

// Build everything.
gulp.task('build', ['clean', 'sprites', 'lint', 'styleguide']);

// Styles, sourcemaps, autoprefixer
gulp.task('styles', function () {
  return gulp.src(options.theme.components + '**/*.scss')
    .pipe($.sassGlob())
    .pipe($.if(!isProduction, $.sourcemaps.init()))
    .pipe($.sass(options.sass)).on('error', $.sass.logError)
    .pipe($.rename({dirname: ''}))
    .pipe($.autoprefixer(options.autoprefixer))
    .pipe($.if(!isProduction,  $.sourcemaps.write('./')))
    .pipe(gulp.dest(options.theme.css))
    .pipe(browserSync.stream({match: '**/*.css'}));
});

// Build style guide.
gulp.task('styleguide', ['styles', 'styleguide:kss-example-chroma'], function () {
  options.styleGuide.css = getCss();
  return $.kss(options.styleGuide);
});

gulp.task('styleguide:kss-example-chroma', function () {
  return gulp.src(options.theme.components + 'style-guide/kss-example-chroma.scss')
    .pipe($.sass(options.sass).on('error', $.sass.logError))
    .pipe($.replace(/(\/\*|\*\/)\n/g, ''))
    .pipe($.rename('kss-example-chroma.twig'))
    .pipe($.size({showFiles: true}))
    .pipe(gulp.dest(options.theme.build + 'twig'));
});

// Debug the generation of the style guide with the --verbose flag.
gulp.task('styleguide:debug', ['clean:styleguide', 'styleguide:kss-example-chroma'], function () {
  options.styleGuide.verbose = true;
  return $.kss(options.styleGuide);
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
gulp.task('watch', ['watch:sass', 'watch:js']);

gulp.task('watch:sass', function () {
  return gulp.watch(options.theme.components + '**/*.scss', options.gulpWatchOptions, function () {
    $.runSequence(
      'styles',
      'lint:sass',
      'browser-sync:reload'
    );
  });
});

gulp.task('watch:styleguide', function () {
  return gulp.watch([
    options.theme.components + '**/*.twig'
  ],
    options.gulpWatchOptions,
    function () {
      $.runSequence(
        'styleguide'
      );
    }
  );
});

gulp.task('watch:js', function () {
  return gulp.watch(options.eslint.files, options.gulpWatchOptions, function () {
    $.runSequence(
      'lint:js'
      // Minify and concat/webpack
    );
  });
});

gulp.task('browser-sync', function () {
  browserSync.init(options.browserSync);
});

gulp.task('browser-sync:reload', function () {
  browserSync.reload();
});

gulp.task('serve', ['build', 'browser-sync', 'watch']);

// Clean all directories.
gulp.task('clean', ['clean:css', 'clean:styleguide']);

// Clean style guide files.
gulp.task('clean:styleguide', function () {
  // You can use multiple globbing patterns as you would with `gulp.src`.
  return $.del([
    options.styleGuide.destination + '*.html',
    options.styleGuide.destination + 'kss-assets',
    options.theme.build + 'twig/*.twig'
  ], {force: true});
});

// Clean CSS files.
gulp.task('clean:css', function () {
  return $.del([
    options.theme.css + '**/*.css',
    options.theme.css + '**/*.map'
  ], {force: true});
});

// Build Sprites.
gulp.task('sprites', function () {
  var spriteData = gulp.src(options.theme.sprites).pipe($.spritesmith(options.sprites));
  return spriteData.pipe(gulp.dest('.'));
});

// Get new css files for kss-node.
function getCss() {
  // Help KSS to automatically find new component CSS files.
  var cssFiles = $.glob.sync('*.css', {cwd: options.theme.css}),
    cssStyleguide = [];

  cssFiles.forEach(function (file) {
    file = $.path.relative(options.rootPath.styleGuide, options.theme.css) + '/' + file;
    cssStyleguide.push(file);
  });
  return cssStyleguide;
}

// Resources used to create this gulpfile.js:
// - https://github.com/google/web-starter-kit/blob/master/gulpfile.babel.js
// - https://github.com/dlmanning/gulp-sass/blob/master/README.md
// - http://www.browsersync.io/docs/gulp/
