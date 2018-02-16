import * as types from '../mutation-types'

export const state = {
  message: null
}

export const getters = {
  message: state => state.message,
  hasMessage: state => state.message != null
}

export const mutations = {
  [types.SET_MESSAGE] (state, message) {
    state.message = message
  }
}

export const actions = {
  setMessage ({commit}, message) {
    commit(types.SET_MESSAGE, message)
  },
}
