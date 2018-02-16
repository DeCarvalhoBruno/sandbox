import axios from 'axios'
// import Cookies from 'js-cookie'
import * as types from '../mutation-types'

function getQueryString (string) {
  let qS = string.match(/\/[^?]+(.*)/)
  return (qS[1]) ? qS[1] : ''
}

// state
export const state = {
  table: [],
  columns: [],
  sortableColumns: []
}

export const getters = {
  table: state => state.table,
  columns: state => state.columns,
  sortableColumns: state => state.sortableColumns
}

export const mutations = {
  [types.FETCH_TABLE_DATA] (state, data) {
    state.table = data.table
    state.columns = data.columns
    state.sortableColumns = data.sortableColumns
  }
}

export const actions = {
  async fetchData ({commit}, {entity, queryString}) {
    await axios.get(`/ajax/admin/${entity}${getQueryString(queryString)}`)
      .then(({data}) => {
        commit(types.FETCH_TABLE_DATA, data)
      })
  }
}
