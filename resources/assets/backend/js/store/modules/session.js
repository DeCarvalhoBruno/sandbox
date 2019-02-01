import * as types from '../mutation-types'
import swal from 'sweetalert2'

export const state = {
  flashMessage: null,
  intendedUrl: null
}

export const getters = {
  flashMessage: state => state.flashMessage,
  intendedUrl: state => state.intendedUrl
}

export const mutations = {
  [types.SET_INTENDED_URL] (state, {url}) {
    state.intendedUrl = url
  },
  [types.SET_FLASH_MESSAGE] (state, {msg}) {
    state.flashMessage = msg
  },
  [types.CHECK_FLASH_MESSAGE] (state) {
    if (state.flashMessage !== null) {
      swal.fire({
        position: 'top-end',
        toast: true,
        type: state.flashMessage.type,
        title: state.flashMessage.text,
        showConfirmButton: false,
        timer: 4000
      })
      state.flashMessage = null
    }
  }
}

export const actions = {
  setIntendedUrl ({commit}, {url}) {
    commit(types.SET_INTENDED_URL, {url})
  },
  setFlashMessage ({commit}, {msg}) {
    commit(types.SET_FLASH_MESSAGE, {msg})
  },
  checkFlashMessage ({commit}) {
    commit(types.CHECK_FLASH_MESSAGE)
  }
}
