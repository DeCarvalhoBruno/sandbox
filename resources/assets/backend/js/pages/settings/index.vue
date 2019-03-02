<template>
    <div class="card">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <b-nav vertical pills>
                        <b-nav-item v-for="(tab,idx) in tabs" :key="idx">
                            <router-link :to="{ name: tab.route }" class="nav-link" active-class="active">
                                <i :class="'fa fa-'+tab.icon"></i>
                                {{ tab.name }}
                            </router-link>
                        </b-nav-item>
                    </b-nav>
                </div>
                <div class="col-md-9 my-5">
                    <router-view/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Vue from 'vue'
  import { Nav } from 'bootstrap-vue/es/components'

  Vue.use(Nav)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'website-index',
    components: {
      Nav
    },
    computed: {
      tabs () {
        return [
          {
            icon: 'cog',
            name: this.$t('general.general'),
            route: 'admin.settings.general'
          },
        ]
      }
    }
  }
</script>