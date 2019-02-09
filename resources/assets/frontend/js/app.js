import i18n from '~/plugins/i18n'
import '~/plugins'
import App from './components/App'

window.Vue = require('vue')

new Vue({
  i18n,
  ...App
})
