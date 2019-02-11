const {src, dest, watch} = require('gulp')
const mjml = require('gulp-mjml')

exports.default = function () {
  return src('resources/emails/tmp/*.mjml')
    .pipe(mjml())
    .pipe(dest('resources/emails/views/'))
}

exports.watch = function () {
  return watch('resources/emails/tmp/*.mjml', ['default'])
}
