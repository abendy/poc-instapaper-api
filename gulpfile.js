'use strict';

const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const concat = require('gulp-concat');
const del = require('del');
const gulp = require('gulp');
const sass = require('gulp-sass');
const uglify = require('gulp-uglify');

const src = 'app/assets/src/';
const dist = 'app/assets/dist/';

//////////////////////////////
// Begin Gulp Tasks
//////////////////////////////

function clean(cb) {
    return del([dist], cb);
}

function scripts() {
    return gulp.src([
        'node_modules/material-design-lite/dist/material.js',
        src + 'js/app.js'
    ])
    .pipe(uglify())
    .pipe(concat('main.js'))
    .pipe(gulp.dest(dist));
}

function styles() {
    return gulp.src([
        src + 'scss/style.scss'
    ])
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(concat('main.css'))
    .pipe(cleanCSS())
    .pipe(gulp.dest(dist))
}

function watch() {
    return gulp.watch(src + 'scss/**/*.scss', gulp.parallel('styles'));
}

const build = gulp.parallel(clean, scripts, styles);

exports.default = build;
exports.build = build;
exports.clean = clean;
exports.styles = styles;
exports.watch = watch;
