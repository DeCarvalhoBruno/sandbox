import './axios'
import './fontawesome'

window.$ = window.jQuery = require('jquery')
window.Popper = require('popper.js').default
import './ziggy'

import Vue from 'vue'
import routeI18n from '~/plugins/route-i18n'

Vue.use(routeI18n)

require('./jquery/sidebar/Layout')
require('./jquery/sidebar/PushMenu')
require('./jquery/sidebar/Tree')
