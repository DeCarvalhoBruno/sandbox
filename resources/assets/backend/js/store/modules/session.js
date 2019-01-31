import * as types from '../mutation-types'

export const state = {
  alertMessage: null,
  intendedUrl: null
}

export const getters = {
  alertMessage: state => state.alertMessage,
  intendedUrl: state => state.intendedUrl
}

export const mutations = {
  [types.SET_INTENDED_URL] (state, {url}) {
    state.intendedUrl = url
  }
}

export const actions = {
  setIntendedUrl ({commit}, {url}) {
    commit(types.SET_INTENDED_URL, {url})
  }
}
