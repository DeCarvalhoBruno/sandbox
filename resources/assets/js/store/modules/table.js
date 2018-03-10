import axios from 'axios'
// import Cookies from 'js-cookie'
import * as types from '../mutation-types'

function getQueryString (string) {
  let qS = string.match(/\/[^?]+(.*)/)
  return (qS[1]) ? qS[1] : ''
}

// state
export const state = {
  rows: [],
  columns: [],
  currentPage: 1,
  from: 0,
  lastPage: 0,
  perPage: 0,
  to: 0,
  total: 0,
  extras: {}
}

export const getters = {
  rows: state => state.rows,
  columns: state => state.columns,
  currentPage: state => state.currentPage,
  from: state => state.from,
  lastPage: state => state.lastPage,
  perPage: state => state.perPage,
  to: state => state.to,
  total: state => state.total,
  extras: state => state.extras
}

export const mutations = {
  [types.FETCH_TABLE_DATA] (state, data) {
    state.rows = data.table.data
    state.columns = data.columns
    state.currentPage = data.table.current_page
    state.from = data.table.from
    state.lastPage = data.table.last_page
    state.perPage = data.table.per_page
    state.to = data.table.to
    state.total = data.table.total
    state.extras = {groups: data.groups}
  },
  [types.UPDATE_TABLE_DATA] (state, data) {
    state.rows = data.table.data
    state.currentPage = data.table.current_page
    state.from = data.table.from
    state.lastPage = data.table.last_page
    state.perPage = data.table.per_page
    state.to = data.table.to
    state.total = data.table.total
    state.extras = {groups: data.groups}
  },
  [types.UPDATE_TABLE_COLUMN] (state, data) {
    state.columns[data.columnName].order = data.direction
  }
}

export const actions = {
  async fetchData ({commit}, {entity, queryString, refresh}) {
    await axios.get(`/ajax/admin/${entity}${getQueryString(queryString)}`)
      .then(({data}) => {
        if (refresh === true) {
          commit(types.UPDATE_TABLE_DATA, data)
        } else {
          commit(types.FETCH_TABLE_DATA, data)
        }
      })
  }
}
