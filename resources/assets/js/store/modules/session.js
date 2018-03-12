import * as types from '../mutation-types'

export const state = {
  message: null,
  variant: 'success'
}

export const getters = {
  message: state => state.message,
  variant: state => state.variant,
  hasMessage: state => state.message != null
}

export const mutations = {
  [types.SET_MESSAGE] (state, data) {
    state.message = data.message
    state.variant = data.variant
  },
  [types.CLEAR_MESSAGE] (state) {
    state.message = null
  }
}

export const actions = {
  setMessageSuccess ({commit}, message) {
    commit(types.SET_MESSAGE, {message, variant: 'success'})
  },
  clearMessage ({commit}) {
    commit(types.CLEAR_MESSAGE)
  }
}
