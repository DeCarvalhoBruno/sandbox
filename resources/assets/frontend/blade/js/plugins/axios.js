import axios from 'axios'

let AxiosException = function Exception (message) { this.message = message }
// Request interceptor
axios.interceptors.request.use(request => {
  let token = document.head.querySelector('meta[name="csrf-token"]')

  request.headers.common['Accept-Language'] = document.querySelector('html')
    .getAttribute('lang')
  request.headers.common['X-Requested-With'] = 'XMLHttpRequest'
  request.headers.common['X-CSRF-TOKEN'] = token.content
  return request
})
// Response interceptor
axios.interceptors.response.use(response => response, error => {
  const {status} = error.response
  const {data} = error.response

  let text
  if (data && data.length > 0) {
    text = error.response.data[0]
  } else {
    text = null
  }

  return Promise.reject(error)
})
