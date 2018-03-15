import * as types from '../mutation-types'

export const state = {
  alertMessage: null,
  alertVariant: 'success',
  modalMessage: null,
  modalVariant: 'default',
  modal: {
    method: '',
    show: false,
    variant: 'default',
    title: '',
    text: '',
    type: 'confirm',
    confirmBtnClass: 'btn-default',
    confirmBtnText: 'OK'
  }
}

export const getters = {
  alertMessage: state => state.alertMessage,
  alertVariant: state => state.alertVariant,
  modal: state => state.modal,
  modalMessage: state => state.modalMessage,
  modalVariant: state => state.modalVariant,
  hasAlertMessage: state => state.alertMessage != null
}

export const mutations = {
  [types.SET_ALERT_MESSAGE] (state, data) {
    state.alertMessage = data.alertMessage
    state.alertVariant = data.alertVariant
  },
  [types.CLEAR_ALERT_MESSAGE] (state) {
    state.alertMessage = null
  },
  [types.SHOW_MODAL] (state, data) {
    let modalDefaults = Object.keys(data.defaults)
    modalDefaults.forEach((val) => {
      if (data.hasOwnProperty(val)) {
        state.modal[val] = data[val]
      }
    })
    state.modal.show = true
  },
  [types.HIDE_MODAL] (state) {
    state.modal.show = false
  }
}

export const actions = {
  setAlertMessageSuccess ({commit}, alertMessage) {
    commit(types.SET_ALERT_MESSAGE, {alertMessage, alertVariant: 'success'})
  },
  clearAlertMessage ({commit}) {
    commit(types.CLEAR_ALERT_MESSAGE)
  },
  showModal ({commit}, {data}) {
    let defaults = {
      method: '',
      show: false,
      variant: 'default',
      title: '',
      text: '',
      type: 'confirm',
      confirmBtnClass: 'btn-default',
      confirmBtnText: 'OK'
    }
    data.defaults = defaults
    commit(types.SHOW_MODAL, data)
  }
}
