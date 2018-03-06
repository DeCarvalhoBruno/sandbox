<template>
    <div id="main_wrapper">
        <navbar/>
        <drawer :menu-items="MenuItems"/>
        <div class="content-wrapper">
            <section class="content-header">
                <div class="link-back">
                    <a @click="$router.go(-1)"><&nbsp;{{$t('general.back')}}</a>
                </div>
                <ol class="breadcrumb">
                    <li v-for="(crumb,index) in breadCrumbs" :key="index">
                        <template v-if="crumb.route!=$router.currentRoute.name">
                            <router-link :to="{ name: crumb.route }">{{crumb.label}}</router-link>
                        </template>
                        <template v-else>
                            <span>{{crumb.label}}</span>
                        </template>
                    </li>
                </ol>
                <div class="container">
                    <b-alert v-if="hasMessage" :show="dismissCountDown"
                             dismissible
                             :variant="variant"
                             @dismissed="dismissCountdown=0"
                             @dismiss-count-down="countDownChanged"
                    >
                        {{message}}
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
        </div>
    </div>
</template>

<script>
  import Vue from 'vue'
  import { mapGetters } from 'vuex'
  import Navbar from '../components/Navbar'
  import Drawer from '../components/Drawer'
  import MenuItems from '../menu-items'
  import { Alert } from 'bootstrap-vue/es/components'

  Vue.use(Alert)

  export default {
    name: 'basic',
    components: {
      Navbar,
      Drawer,
      Alert
    },
    data: function () {
      return {
        MenuItems,
        dismissSecs: 5,
        dismissCountDown: 0,
        breadCrumbs: []
      }
    },
    computed: mapGetters({
      hasMessage: 'session/hasMessage',
      message: 'session/message',
      variant: 'session/variant'
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
      countDownChanged (dismissCountDown) {
        this.dismissCountDown = dismissCountDown
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