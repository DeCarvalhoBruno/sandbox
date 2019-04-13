require('dotenv').config()

const env = process.env
const EchoServer = require('laravel-echo-server')

const options = {
  authHost: env.ECHO_SERVER_AUTH_HOST,
  devMode: env.ECHO_SERVER_DEV_MODE,
  host: env.ECHO_SERVER_HOST,
  port: env.ECHO_SERVER_PORT,
  database: 'redis',
  protocol: env.ECHO_SERVER_PROTOCOL,
  databaseConfig: {
    redis: {
      password: env.REDIS_PASSWORD,
      db: env.BROADCAST_REDIS_DB,
      port: env.REDIS_PORT
    }
  },
  apiOriginAllow: {
    allowCors: false,
    allowOrigin: '',
    allowMethods: '',
    allowHeaders: ''
  }
}

EchoServer.run(options)
