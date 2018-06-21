const path = require('path')
const mix = require('laravel-mix')
const CleanWebpackPlugin = require('clean-webpack-plugin')
const webpack = require('webpack')

const folderName = '1b8eb'
const publicPath = `public/${folderName}`

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
  proxy: 'laravel.local/admin/login',
  files: [
    `public/${folderName}/**/*`,
    'app/**/*'
  ]
})
mix.webpackConfig({
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
    })
  ],
  resolve: {
    extensions: ['.js', '.json', '.vue'],
    alias: {
      '~': path.join(__dirname, './resources/assets/backend/js'),
      'jquery': 'jquery/dist/jquery.min.js',
      'popper': 'popper.js/dist/popper.min.js'
    }
  },
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: `/${folderName}/`
  }
})
