import Vue from 'vue'
import App from './components/App'
import 'front_path/plugins'
import i18n from 'front_path/plugins/i18n'
import { loadMessages } from './plugins/i18n'

(async function () {
  await loadMessages()
})().then(() => {
  new Vue({
    i18n: i18n,
    ...App
  }).$mount('#app')
})
