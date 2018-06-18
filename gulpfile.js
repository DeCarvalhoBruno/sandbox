//So as not to get the promise reference not found on my old and non upgradable version of node
require('es6-promise').polyfill()

var gulp = require('gulp')
var mjml = require('gulp-mjml')
var mjmlEngine = require('mjml')

Object.assign = require('object-assign')

gulp.task('default', function () {
  gulp.src('resources/emails/tmp/*.mjml')
    .pipe(mjml(mjmlEngine,
      {fonts: {Lato: 'https://fonts.googleapis.com/css?family=Lato:300,400,500,700'}}))
    .pipe(gulp.dest('resources/emails/views/'))
})

gulp.task('watch', function () {
  gulp.watch('resources/emails/tmp/*.mjml', ['default'])
})
