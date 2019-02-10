import i18n from 'front_path/plugins/i18n'
import 'front_path/plugins'
import App from './components/App'

window.Vue = require('vue')

new Vue({
  i18n,
  ...App
})
