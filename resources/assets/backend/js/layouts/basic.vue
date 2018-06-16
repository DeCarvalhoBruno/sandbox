<template>
    <div id="main_wrapper">
        <navbar/>
        <drawer :menu-items="MenuItems"/>
        <div class="content-wrapper">
            <section class="content-header">
                <div id="top_link_container" class="container">
                    <div class="row">
                        <div class="link-back" v-if="hasBreadCrumbs()">
                            <a @click="$router.go(-1)"><&nbsp;{{$t('general.back')}}</a>
                        </div>
                        <ol class="breadcrumb">
                            <li v-for="(crumb,index) in breadCrumbs" :key="index">
                                <template v-if="crumb.route!==$router.currentRoute.name">
                                    <router-link
                                            :to="{ name: crumb.route }">{{crumb.label}}
                                    </router-link>
                                </template>
                                <template v-else>
                                    <span>{{crumb.label}}</span>
                                </template>
                            </li>
                        </ol>
                    </div>
                </div>
                <hr>
                <div class="container">
                    <b-alert v-if="hasAlertMessage" :show="dismissCountDown"
                             dismissible
                             :variant="alertVariant"
                             @dismissed="alertDismissed"
                             @dismiss-count-down="countDownChanged">
                        {{alertMessage}}
                    </b-alert>
                </div>
            </section>
            <section class="content">
                <transition name="page" mode="out-in">
                    <div class="container-fluid">
                        <slot>
                            <router-view/>
                        </slot>
                    </div>
                </transition>
            </section>
            <b-modal v-model="modal.show">
                <div slot="modal-header">
                    <h5>{{modal.title}}</h5>
                </div>
                <div class="d-block text-center">
                    <h3 v-html="modal.text"></h3>
                </div>
                <div slot="modal-footer">
                    <template v-if="modal.type==='confirm'">
                        <button type="button" class="btn"
                                :class="modal.confirmBtnClass"
                                @click="$root.$emit('modal_confirmed',modal.method)"
                        >{{modal.confirmBtnText}}
                        </button>
                        <button
                                type="button"
                                class="btn btn-secondary"
                                @click="$store.commit('session/HIDE_MODAL')">{{$t('general.cancel')}}
                        </button>
                    </template>
                </div>
            </b-modal>
        </div>
    </div>
</template>

<script>
  import Vue from 'vue'
  import { mapGetters } from 'vuex'
  import Navbar from '../components/Navbar'
  import Drawer from '../components/Drawer'
  import MenuItems from '../menu-items'
  import { Alert, Modal } from 'bootstrap-vue/es/components'

  Vue.use(Alert)
  Vue.use(Modal)

  export default {
    name: 'basic',
    components: {
      Navbar,
      Drawer,
      Alert,
      Modal
    },
    data: function () {
      return {
        MenuItems,
        dismissSecs: 10,
        dismissCountDown: 0,
        breadCrumbs: []
      }
    },
    computed: mapGetters({
      hasAlertMessage: 'session/hasAlertMessage',
      alertMessage: 'session/alertMessage',
      alertVariant: 'session/alertVariant',
      modal: 'session/modal'
    }),
    watch: {
      '$route' () {
        this.breadCrumbs = this.makeBreadcrumbs(this.$router.currentRoute).reverse()
        this.dismissCountDown = this.dismissSecs
      }
    },
    mounted () {
      this.dismissCountDown = this.dismissSecs
    },
    methods: {
      hasBreadCrumbs () {
        return this.breadCrumbs.length > 2
      },
      alertDismissed () {
        this.dismissCountdown = 0
        this.$store.dispatch('session/clearAlertMessage')
      },
      countDownChanged (dismissCountDown) {
        this.dismissCountDown = dismissCountDown
        if (dismissCountDown === 0) {
          this.$store.dispatch('session/clearAlertMessage')
        }
      },
      makeBreadcrumbs (route, path = [], child = false) {
        if (!child) {
          path.push(this.translateRouteName(route.name))
          if (typeof route.matched[0] == 'object') {
            if (typeof route.matched[0].meta.parent == 'string') {
              this.makeBreadcrumbs(this.$router.resolve({name: route.matched[0].meta.parent}), path, true)
            }
          }
        } else {
          if (typeof route.resolved.meta.parent == 'string') {
            path.push(this.translateRouteName(route.route.name))
            this.makeBreadcrumbs(this.$router.resolve({name: route.resolved.meta.parent}), path, true)
          } else {
            path.push(this.translateRouteName(route.location.name))
          }
        }
        return path
      },
      translateRouteName (route) {
        return {route: route, label: this.$t('breadcrumb.' + (route.replace(/\.+/g, '-')))}
      }
    }
  }
</script>