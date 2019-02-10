const path = require('path')
const mix = require('laravel-mix')
const CleanWebpackPlugin = require('clean-webpack-plugin')
const webpack = require('webpack')
require('dotenv').config()

const folderName = '1b8eb'
const publicPath = `public/${folderName}`

Mix.listen('configReady', function (config) {
  const rules = config.module.rules
  const targetRegex = /(\.(png|jpe?g|gif)$|^((?!font).)*\.svg$)/

  for (let rule of rules) {
    if (rule.test.toString() == targetRegex.toString()) {
      rule.exclude = /\.svg$/
      break
    }
  }
})

mix.copy('resources/assets/_fonts/aladin-v6-latin-regular.ttf',
  `${path.resolve(__dirname, 'public')}/fonts`)
mix.js('resources/assets/backend/js/app.js', 'js/app.js')
  .sass('resources/assets/backend/sass/app.scss', 'css/app.css')
  .sourceMaps()
  .disableNotifications()
  .setPublicPath(path.normalize(publicPath))
  .options({
    fileLoaderDirs: {
      fonts: '../fonts'
    }
  })

if (mix.inProduction()) {
  mix.version()

  // mix.extract([
  //   'vue',
  //   'vform',
  //   'axios',
  //   'vuex',
  //   'jquery',
  //   'popper.js',
  //   'vue-i18n',
  //   'vue-meta',
  //   'js-cookie',
  //   'vue-router',
  //   'sweetalert2',
  //   'vuex-router-sync',
  //   '@fortawesome/fontawesome',
  //   '@fortawesome/vue-fontawesome'
  // ])
}
mix.browserSync({
  proxy: process.env.APP_URL + '/admin/login',
  browser: 'chrome',
  files: [
    `public/${folderName}/**/*`,
    'app/**/*',
    'resources/assets/**/*'
  ]
})
mix.webpackConfig({
  module: {
    rules: [
      {
        test: /\.svg$/,
        use: [
          {
            loader: 'html-loader',
            options: {
              minimize: true
            }
          }]
      }]
  },
  plugins: [
    new CleanWebpackPlugin([
      'js', 'css', 'fonts', 'mix-manifest.json'
    ], {
      root: path.resolve(__dirname, publicPath),
      exclude: 'vendor',
      verbose: true,
      dry: false
    }),
    new webpack.ProvidePlugin({
      '$': 'jquery',
      jQuery: 'jquery',
      Popper: 'popper'
      // Promise: ['es6-promise', 'Promise']
    })
  ],
  resolve: {
    extensions: ['.js', '.json', '.vue'],
    alias: {
      'front_path': path.resolve(path.join(__dirname, './resources/assets/frontend/js')),
      'back_path': path.resolve(path.join(__dirname, './resources/assets/backend/js')),
      'jquery': 'jquery/dist/jquery.min.js',
      'popper': 'popper.js/dist/popper.min.js'
    }
  },
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: `/${folderName}/`
  }
})
