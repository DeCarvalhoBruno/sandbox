const path = require('path')
const mix = require('laravel-mix')
const CleanWebpackPlugin = require('clean-webpack-plugin')
const webpack = require('webpack')
require('dotenv').config()

const folderName = '6aa0e'
const publicPath = `public/${folderName}`

mix.js('resources/assets/frontend/js/app.js', 'js/app.js')
  .sass('resources/assets/frontend/sass/app.scss', 'css/app.css')
  .sourceMaps()
  .disableNotifications()
  .setPublicPath(path.normalize(publicPath))
  .autoload({
    // 'exports-loader?Util!bootstrap/js/dist/util': ['Util'],
    // 'exports-loader?Dropdown!bootstrap/js/dist/dropdown': ['Dropdown'],
    // 'exports-loader?Tooltip!bootstrap/js/dist/tooltip': ['Tooltip'],
    // 'exports-loader?Tab!bootstrap/js/dist/tab': ['Tab']
  })
  .options({
    fileLoaderDirs: {
      fonts: '../fonts'
    }
  })

if (mix.inProduction()) {
  mix.version()

  mix.extract([
    'vue',
    'axios',
    'jquery',
    'popper.js',
    'vue-i18n',
    'sweetalert2',
    '@fortawesome/fontawesome',
    '@fortawesome/vue-fontawesome'
  ])
}
mix.browserSync({
  proxy: process.env.APP_URL,
  browser: 'chrome',
  notify: false,
  files: [
    `public/${folderName}/**/*`,
    'resources/views/**/*',
    'app/**/*',
    'naraki/**/*'
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
      // '$': 'jquery',
      // jQuery: 'jquery',
      // // Popper: 'popper.js/dist/umd/popper',
      // axios: 'axios/dist/axios.min.js',
      // ResponsiveBootstrapToolkit: 'front_path/plugins/jquery/bootstrap-toolkit.js',
      // swal: 'sweetalert2/dist/sweetalert2.min.js'
    })
  ],
  resolve: {
    extensions: ['.js', '.json', '.vue'],
    alias: {
      'front_path': path.resolve(path.join(__dirname, './resources/assets/frontend/js')),
      'back_path': path.resolve(path.join(__dirname, './resources/assets/backend/js')),
      'jquery': 'jquery/src/jquery'
    },
    modules: [
      'node_modules',
      path.resolve(__dirname, 'jquery-lazy')
    ]
  },
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: `/${folderName}/`
  }
})
