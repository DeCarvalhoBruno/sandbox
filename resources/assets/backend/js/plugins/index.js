import './axios'
import './fontawesome'
import Echo from 'laravel-echo'
window.io = require('socket.io-client')

require('./jquery/sidebar/Layout')
require('./jquery/sidebar/PushMenu')
require('./jquery/sidebar/Tree')

window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: '192.168.0.10:6101'
})
window.Echo.channel('general').listen('.emailing.subscription', (e) => {
  console.log(e)
})
