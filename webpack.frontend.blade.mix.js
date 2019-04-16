const path = require('path')
const mix = require('laravel-mix')
const CleanWebpackPlugin = require('clean-webpack-plugin')
const webpack = require('webpack')
require('dotenv').config()

const assetFolder = 'vendor/naraki/components'
const folderName = '6aa0e'
const publicPath = `public/${folderName}`

mix.js('resources/assets/app.js', 'js/app.js')
  .sass('resources/assets/app.scss', 'css/app.css', {
    includePaths: [
      // path.resolve(path.join(__dirname, './resources/assets')),
      path.resolve(assetFolder + '/resources')
    ]
  })
  .sourceMaps()
  .disableNotifications()
  .setPublicPath(path.normalize(publicPath))
  .options({
    // autoprefixer: {
    //   options: {
    //     browsers: ['> 10%', 'last 6 versions', 'ff ESR', 'opera >= 12', 'safari >= 5', 'ios >= 8', 'ie >= 8'],
    //   }
    // },
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
    'fontawesome'
  ])
}
mix.browserSync({
  proxy: process.env.APP_URL,
  browser: 'chrome',
  notify: false,
  files: [
    'app/**/*',
    assetFolder + '/**/*'
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
      'front_path': path.resolve(assetFolder + '/resources/frontend/js'),
      'front_main_path': path.resolve('resources/assets'),
      'back_path': path.resolve(assetFolder + '/resources/backend/js'),
      'jquery': 'jquery/src/jquery'
    },
    modules: [
      'node_modules'
    ]
  },
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: `/${folderName}/`
  }
})
