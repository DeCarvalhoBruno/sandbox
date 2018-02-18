import * as types from '../mutation-types'

export const state = {
  message: null,
  variant: null
}

export const getters = {
  message: state => state.message,
  variant: state => state.variant,
  hasMessage: state => state.message != null
}

export const mutations = {
  [types.SET_MESSAGE] (state, message, variant) {
    state.message = message
    state.variant = variant
  }
}

export const actions = {
  setMessage ({commit}, message, variant = 'success') {
    commit(types.SET_MESSAGE, message, variant)
  }
}
