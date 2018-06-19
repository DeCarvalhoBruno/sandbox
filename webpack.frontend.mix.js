const path = require('path')
const mix = require('laravel-mix')
const CleanWebpackPlugin = require('clean-webpack-plugin')
const webpack = require('webpack')

const folderName = '6aa0e'
const publicPath = `public/${folderName}`

mix.js('resources/assets/frontend/js/app.js', 'js/app.js')
  .sass('resources/assets/frontend/sass/app.scss', 'css/app.css')
  .sourceMaps()
  .disableNotifications()
  .setPublicPath(path.normalize(publicPath))
  .autoload({
    'exports-loader?Util!bootstrap/js/dist/util': ['Util'],
    'exports-loader?Tooltip!bootstrap/js/dist/dropdown': ['Dropdown'],
    'exports-loader?Tooltip!bootstrap/js/dist/tooltip': ['Tooltip']
  })
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
  proxy: 'laravel.local/login',
  files: [
    `public/${folderName}/**/*`,
    'resources/views/**/*',
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
      Popper: 'popper.js/dist/umd/popper',
      axios: 'axios/dist/axios.min.js'
    })
  ],
  resolve: {
    extensions: ['.js', '.json', '.vue'],
    alias: {
      '~': path.join(__dirname, './resources/assets/frontend/js'),
      'jquery': 'jquery/dist/jquery.min.js'
    }
  },
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: `/${folderName}/`
  }
})
