import axios from 'axios'

axios.interceptors.request.use(request => {
  const {token} = window.config

  request.headers.common['Accept-Language'] = document.querySelector('html')
    .getAttribute('lang')
  request.headers.common['X-Requested-With'] = 'XMLHttpRequest'
  request.headers.common['Authorization'] = `Bearer ${token}`
  return request
})

axios.interceptors.response.use(response => response, error => {
  const {status} = error.response
  const {data} = error.response
console.log(error)
  let text
  if (data && data.length > 0) {
    text = error.response.data[0]
  } else {
    text = null
  }

  return Promise.reject(error)
})
