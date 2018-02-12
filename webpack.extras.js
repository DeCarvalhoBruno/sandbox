const path = require('path')
const webpack = require('webpack')
// const webpackUglifyJsPlugin = require('webpack-uglify-js-plugin')

module.exports = {
  entry: {
    main: ['./resources/assets/js/plugins/polyfills.js']
  },
  output: {
    path: path.resolve(__dirname, 'public/js'),
    filename: 'extras.js',
    pathinfo: true
  },
  module: {
    rules: [

    ]
  },
  plugins: [
    new webpack.optimize.UglifyJsPlugin({
      minimize: true,
      sourceMap: false,
      output: {
        comments: false
      },
      compressor: {
        warnings: false
      }
    })
  ]
}
