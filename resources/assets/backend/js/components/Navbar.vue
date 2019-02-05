<template>
    <header class="main-header">
        <!-- Logo -->
        <div v-if="user" id="logo_wrapper">
            <a href="index2.html" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">LV</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">Laravel</span>
            </a>
        </div>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark main-header navbar-static-top">
            <button v-if="user" class="navbar-toggler" type="button"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <fa icon="bars"></fa>
            </button>
            <div v-if="user" class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <span><a class="nav-link sidebar-toggle"  id="button-sidebar-trigger"
                                 data-toggle="push-menu" role="button"><fa icon="bars"></fa></a></span>
                    </li>
                </ul>
                <ul class="navbar-nav nav justify-content-end">
                    <div class="btn-nav-group">
                        <div class="btn-nav">
                            <router-link :to="{ name: 'admin.settings.profile' }"
                                         :title="$t('general.settings')">
                                <fa icon="envelope" fixed-width size="lg"></fa>
                            </router-link>
                        </div>
                    </div>
                    <b-dropdown right :text="user.username" variant="dark">
                        <b-dropdown-item router-tag="a" :to="{ name: 'admin.settings.profile' }"
                        ><fa icon="cog" fixed-width></fa>{{ $t('general.settings') }}</b-dropdown-item>
                        <b-dropdown-divider/>
                        <b-dropdown-item-button
                                @click="logout"><fa icon="sign-out-alt" fixed-width/>{{ $t('general.logout') }}</b-dropdown-item-button>
                    </b-dropdown>
                </ul>
            </div>
        </nav>
    </header>
</template>

<script>
  import Vue from 'vue'
  import { mapGetters } from 'vuex'
  import { Dropdown, Link } from 'bootstrap-vue/es/components'

  Vue.use(Dropdown)

  export default {
    data: function () {
      return {
        appName: window.config.appName
      }
    },
    components: {
      Dropdown, Link
    },
    computed: mapGetters({
      user: 'auth/user'
    }),
    methods: {
      async logout () {
        // Log out the user.
        await this.$store.dispatch('auth/logout')

        // Redirect to login.
        this.$router.push({name: 'admin.login'})
      }
    }
  }
</script>