import Vue from 'vue'
import store from '~/store'
import i18n from '~/plugins/i18n'
import router from '~/router'
import App from '~/components/App'

import '~/plugins'

new Vue({
  i18n,
  store,
  router,
  ...App
})
