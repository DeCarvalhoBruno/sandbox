import axios from 'axios'
import store from '~/store'
import router from '~/router'
import swal from 'sweetalert2'
import i18n from '~/plugins/i18n'

// Request interceptor
axios.interceptors.request.use(request => {
  const token = store.getters['auth/token']
  if (token) {
    request.headers.common['Authorization'] = `Bearer ${token}`
  }

  const locale = store.getters['lang/locale']
  if (locale) {
    request.headers.common['Accept-Language'] = locale
  }
  return request
})

// Response interceptor
axios.interceptors.response.use(response => response, error => {
  const {status} = error.response

  if (status >= 500) {
    swal({
      type: 'error',
      title: i18n.t('modal.error.h'),
      text: i18n.t('modal.error.t'),
      reverseButtons: true,
      confirmButtonText: i18n.t('general.ok')
    })
  }

  if (status === 401 && store.getters['auth/check']) {
    swal({
      type: 'warning',
      title: i18n.t('modal.token_expired.h'),
      text: i18n.t('modal.token_expired.t'),
      reverseButtons: true,
      confirmButtonText: i18n.t('ok'),
      cancelButtonText: i18n.t('cancel')
    }).then(async () => {
      await store.dispatch('auth/logout')
      router.push({name: 'login'})
    })
  }

  if (status === 403) {
    swal({
      type: 'error',
      title: i18n.t('modal.unauthorized.h'),
      text: i18n.t('modal.unauthorized.t'),
      reverseButtons: true,
      confirmButtonText: i18n.t('general.ok')
    })
  }

  return Promise.reject(error)
})
