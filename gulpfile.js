/*
 *
 * Filename: gulp.js
 * Date: 01-06-2021
 * Description: gulps assets from public/ to static/
 * Author: Iyad Al-Kassab @ SKITSC
 *
 */

var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');

var browserSync = require('browser-sync').create();

var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var del = require('del');

gulp.task('clean', del.bind(null, ['dist']));

gulp.task('browserSync', done => {

    browserSync.init({
        server: {
            baseDir: "./public"
        }
    });
    done();
});

gulp.task('sass', done => {

  gulp.src('./public/scss/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./static/css'))
    .pipe(browserSync.reload({
        stream: true
    }));;

    done();
});

gulp.task('script', gulp.series('clean'), () => {

    return gulp.src('./public/js/**/*.js')
        .pipe(gulpif('*.js', uglify()))
        .pipe(gulp.dest('./dist/js'));
});

gulp.task('fonts', gulp.series('clean'), () => {
  
    return gulp.src('./public/fonts/**/*.{eot, svg, ttf, woff, woff2}')
        .pipe(gulp.dest('./dist/fonts'));
});

gulp.task('watch', gulp.series('sass'), function() {
    browserSync(browserSyncSetting);

    gulp.watch(
        ['./public/js/**/*.js','!dist/**/*'], 
        { cwd: './' }, reload
    );

    gulp.watch('./public/scss/**/*.scss', ['sass']);
});

gulp.task('build', gulp.series('sass', 'fonts', 'script'), () => {
    
    return gulp.dest('dist/**/*');
});