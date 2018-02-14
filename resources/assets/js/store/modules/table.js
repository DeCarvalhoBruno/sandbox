import axios from 'axios'
// import Cookies from 'js-cookie'
import * as types from '../mutation-types'

// state
export const state = {
  table: []
}

export const getters = {
  table: state => state.table
}

export const mutations = {
  [types.FETCH_TABLE_DATA] (state, data) {
    state.table = data
  }
}

export const actions = {
  async fetchData ({commit}, options) {
    await axios.get(`/ajax/admin/${options.entity}`).then(({data}) => {
      commit(types.FETCH_TABLE_DATA, data)
    })
  }
}
