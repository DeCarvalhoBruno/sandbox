import Cookies from 'js-cookie'
import * as types from '../mutation-types'

const locales = {
  'en': 'EN',
  'fr': 'FR'
}
const {locale} = window.config

// state
export const state = {
  locale: Cookies.get('locale') || locale,
  locales: locales,
  fallback: 'en'
}

// getters
export const getters = {
  locale: state => state.locale,
  locales: state => state.locales,
  fallback: state => state.fallback
}

// mutations
export const mutations = {
  [types.SET_LOCALE] (state, {locale}) {
    state.locale = locale
  }
}

// actions
export const actions = {
  setLocale ({commit}, {locale}) {
    commit(types.SET_LOCALE, {locale})

    Cookies.set('locale', locale, {expires: 365})
  }
}
