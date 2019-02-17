import './axios'
import './fontawesome'
import Echo from 'laravel-echo'
import io from 'socket.io-client'
import store from 'back_path/store'

require('./jquery/sidebar/Layout')
require('./jquery/sidebar/PushMenu')
require('./jquery/sidebar/Tree')

const token = store.getters['auth/token']

window.Echo = new Echo({
  broadcaster: 'socket.io',
  client: io,
  transports: ['websocket'],
  host: `${process.env.MIX_ECHO_SERVER_HOST}:${process.env.MIX_ECHO_SERVER_PORT}`,
  auth: {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  }
})

window.Echo.private('general').listen('.emailing.subscription', (e) => {

})
