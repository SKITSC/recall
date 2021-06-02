/*
 *
 * Filename: gulp.js
 * Date: 01-06-2021
 * Description: gulps assets from public/ to static/
 * Author: Iyad Al-Kassab @ SKITSC
 *
 */

var gulp = require('gulp');
var rename = require('gulp-rename');

var sass = require('gulp-sass');
var cleancss = require('gulp-clean-css');
var sourcemaps = require('gulp-sourcemaps');

var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var obfuscator = require('gulp-javascript-obfuscator');

var browserSync = require('browser-sync').create();

var del = require('del');

gulp.task('clean', done => {

    del.sync(['/static/*']);
    done();
});

gulp.task('watch', () => {

    browserSync.init({
        server: {
            baseDir: "./static"
        }
    });

    gulp.watch('./public/scss/*.scss').on('change', gulp.series('sass', 'css')); //compile sass and minify css
    gulp.watch('./public/js/*.js').on('change', gulp.series('script')); //uglify js
});

gulp.task('sass', () => {

    return gulp.src('./public/scss/*.scss')
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(sass({outputStyle:'compressed'}).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(cleancss())
        .pipe(rename({suffix:'.min'}))
        .pipe(gulp.dest('./static/css/'));
});

gulp.task('script', () => {

    return gulp.src('./public/js/*.js')
        .pipe(gulpif('*.js', uglify()))
        .pipe(obfuscator({compact:true}))
        .pipe(rename({suffix:'.min'}))
        .pipe(gulp.dest('./static/js/'));
});

gulp.task('fonts', () => {
  
    return gulp.src('./public/fonts/*.{eot, svg, ttf, woff, woff2}')
        .pipe(gulp.dest('./static/fonts/'));
});

gulp.task('build', gulp.series('clean', 'sass', 'script', 'fonts'), () => {
    
    return gulp.dest('./static');
});