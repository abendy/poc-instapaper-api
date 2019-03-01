'use strict';

const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const concat = require('gulp-concat');
const del = require('del');
const gulp = require('gulp');
const notify = require('gulp-notify');
const plumber = require('gulp-plumber');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');

const src = 'app/assets/src/';
const dist = 'app/assets/dist/';

/* Material Design Lite location */
const mdl = 'node_modules/material-design-lite/';

//////////////////////////////
// Begin Gulp Tasks
//////////////////////////////

function clean(cb) {
    return del([dist], cb);
}

function scripts() {
    return gulp.src([src + 'js/app.js'])
    .pipe(sourcemaps.init())
    .pipe(concat('main.js'))
    .pipe(gulp.dest(dist))
    .pipe(uglify())
    .pipe(concat('main.min.js'))
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest(dist));
}

var onError = function(err) {
    notify.onError({
        title:    "Gulp",
        subtitle: "Failure!",
        message:  "Error: <%= error.message %>"
    })(err);
    this.emit('end');
};

function styles() {
    return gulp.src([
        src + 'scss/style.scss'
    ])
    .pipe(plumber({errorHandler: onError}))
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'nested'}).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(concat('main.css'))
    .pipe(gulp.dest(dist))
    .pipe(cleanCSS())
    .pipe(concat('main.min.css'))
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest(dist))
}

function images() {
    return gulp.src([
        mdl + 'dist/images/**/*'
    ])
    .pipe(gulp.dest(dist + 'images'));
}

function watch() {
    return gulp.watch(src + 'scss/**/*.scss', gulp.parallel('styles'));
}

const build = gulp.series(clean, scripts, styles, images);

exports.default = build;
exports.build = build;
exports.clean = clean;
exports.scripts = scripts;
exports.styles = styles;
exports.images = images;
exports.watch = watch;
