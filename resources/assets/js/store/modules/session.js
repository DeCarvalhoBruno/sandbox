import * as types from '../mutation-types'

export const state = {
  alertMessage: null,
  alertVariant: 'success',
  modalMessage: null,
  modalVariant: 'default',
  modal: {
    show: false,
    message: null,
    variant: 'default',
    title: ''
  }
}

export const getters = {
  alertMessage: state => state.alertMessage,
  alertVariant: state => state.alertVariant,
  modal: state => state.modal,
  modalMessage: state => state.modalMessage,
  modalVariant: state => state.modalVariant,
  hasMessage: state => state.alertMessage != null
}

export const mutations = {
  [types.SET_ALERT_MESSAGE] (state, data) {
    state.alertMessage = data.alertMessage
    state.alertVariant = data.alertVariant
  },
  [types.CLEAR_ALERT_MESSAGE] (state) {
    state.alertMessage = null
  },
  [types.SET_MODAL] (state, data) {
    state.modal = data
  }
}

export const actions = {
  setAlertMessageSuccess ({commit}, alertMessage) {
    commit(types.SET_ALERT_MESSAGE, {alertMessage, alertVariant: 'success'})
  },
  clearAlertMessage ({commit}) {
    commit(types.CLEAR_ALERT_MESSAGE)
  },
  setModal ({commit}, {params}) {
    commit(types.SET_MODAL, params)
  }

}
