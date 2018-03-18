<template>
    <b-card :title="$t('general.settings')" no-body>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <b-nav vertical pills>
                        <b-nav-item v-for="(tab,idx) in tabs" :key="idx">
                            <router-link :to="{ name: tab.route }" class="nav-link" active-class="active">
                                <fa :icon="tab.icon" fixed-width/>
                                {{ tab.name }}
                            </router-link>
                        </b-nav-item>
                    </b-nav>
                </div>
                <div class="col-md-8">
                    <transition name="fade" mode="out-in">
                        <router-view/>
                    </transition>
                </div>
            </div>
        </div>
    </b-card>
</template>

<script>
  import Vue from 'vue'
  import { Card, Nav } from 'bootstrap-vue/es/components'

  Vue.use(Card)
  Vue.use(Nav)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'settings-index',
    components: {
      Card,
      Nav
    },
    computed: {
      tabs () {
        return [
          {
            icon: 'user',
            name: this.$t('general.profile'),
            route: 'admin.settings.profile'
          },
          {
            icon: 'lock',
            name: this.$t('general.password'),
            route: 'admin.settings.password'
          }
        ]
      }
    }
  }
</script>