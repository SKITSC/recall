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

var imagemin = require('gulp-imagemin');

var browserSync = require('browser-sync').create();

var del = require('del');

gulp.task('clean', done => {

    del.sync(['/src/static/*']);
    done();
});

gulp.task('watch', () => {

    browserSync.init({
        proxy: "localhost/plivo_backup/src/",
        online: true
    });

    gulp.watch('./public/scss/*.scss').on('change', gulp.series('sass')); //compile sass and minify css
    gulp.watch('./public/js/*.js').on('change', gulp.series('scripts')); //uglify and obfuscate js
    gulp.watch('./public/fonts/*').on('change', gulp.series('fonts')); //move fonts
    gulp.watch('./public/img/*').on('change', gulp.series('images')); //minify images
});

gulp.task('sass', () => {

    return gulp.src('./public/scss/*.scss')
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(sass({outputStyle:'compressed'}).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(cleancss())
        .pipe(rename({suffix:'.min'}))
        .pipe(gulp.dest('./src/static/css/'))
        .pipe(browserSync.stream());
});

gulp.task('scripts', () => {

    return gulp.src('./public/js/*.js')
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(gulpif('*.js', uglify()))
        .pipe(obfuscator({compact:true}))
        .pipe(sourcemaps.write())
        .pipe(rename({suffix:'.min'}))
        .pipe(gulp.dest('./src/static/js/'))
        .pipe(browserSync.stream());
});

gulp.task('fonts', () => {
  
    return gulp.src('./public/fonts/*.{eot, svg, ttf, woff, woff2}')
        .pipe(gulp.dest('./src/static/fonts/'))
        .pipe(browserSync.stream());
});

gulp.task('images', () => {

    return gulp.src('./public/img/*')
        .pipe(imagemin())
        .pipe(gulp.dest('./src/static/img/'))
        .pipe(browserSync.stream());
});

gulp.task('build', gulp.series('clean', 'sass', 'scripts', 'fonts', 'images'), () => {
    
    return gulp.dest('./src/static');
});