const path = require('path')
const mix = require('laravel-mix')
const CleanWebpackPlugin = require('clean-webpack-plugin')
// const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer')

// mix.autoload({
//   jquery: ['$', 'jQuery']
//   // 'exports-loader?Util!bootstrap/js/dist/util': ['Util'],
//   // 'exports-loader?Modal!bootstrap/js/dist/modal': ['Modal']
// })

mix.js('resources/assets/js/app.js', 'public/js')
  .sass('resources/assets/sass/app.scss', 'public/css')
  .sourceMaps()
  .disableNotifications()

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
mix.browserSync('laravel.lti.local/admin/login')
// new BundleAnalyzerPlugin()
mix.webpackConfig({
  plugins: [
    new CleanWebpackPlugin([
      'js'
    ], {
      root: path.resolve(__dirname, 'public'),
      exclude: 'vendor',
      verbose: true,
      dry: false
    })
  ],
  resolve: {
    extensions: ['.js', '.json', '.vue'],
    alias: {
      '~': path.join(__dirname, './resources/assets/js')
    }
  },
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: mix.config.hmr ? '//localhost:8080' : '/'
  }
})
