import axios from 'axios'
import Cookies from 'js-cookie'
import * as types from '../mutation-types'

const {user} = window.config

// state
export const state = {
  user,
  token: Cookies.get('token')
}

// getters
export const getters = {
  user: state => state.user,
  token: state => state.token,
  check: state => state.user !== null
}

// mutations
export const mutations = {
  [types.SAVE_TOKEN] (state, {token, remember}) {
    state.token = token
    Cookies.set('token', token, {expires: remember ? 365 : null})
  },

  [types.FETCH_USER_SUCCESS] (state, {user}) {
    state.user = user
  },

  [types.FETCH_USER_FAILURE] (state) {

  },
  [types.LOGOUT] (state) {
    state.user = null
  },

  [types.UPDATE_USER] (state, {user}) {
    state.user = user
  }
}

// actions
export const actions = {
  saveToken ({commit, dispatch}, payload) {
    commit(types.SAVE_TOKEN, payload)
  },

  updateUser ({commit}, payload) {
    commit(types.UPDATE_USER, payload)
  },

  revokeUser ({commit}) {
    commit(types.LOGOUT)
  },

  async logout ({commit}) {
    await axios.post('/admin/logout')
    commit(types.LOGOUT)
  }

}
