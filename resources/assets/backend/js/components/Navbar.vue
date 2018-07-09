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
            <button v-if="user" class="navbar-toggler" type="button" data-toggle="push-menu"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <fa icon="bars"/>
            </button>
            <div v-if="user" class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <span><a class="nav-link sidebar-toggle"
                                 data-toggle="push-menu" role="button"><fa icon="bars"/></a></span>
                    </li>
                </ul>

                <b-dropdown right :text="user.username" variant="dark" class="navbar-nav ml-auto">
                    <b-dropdown-item>
                        <router-link :to="{ name: 'admin.settings.profile' }">
                            <fa icon="cog" fixed-width/>
                            {{ $t('general.settings') }}
                        </router-link>

                    </b-dropdown-item>
                    <b-dropdown-divider/>
                    <b-dropdown-item-button @click="logout">
                        <fa icon="sign-out-alt" fixed-width/>
                        {{ $t('general.logout') }}
                    </b-dropdown-item-button>
                </b-dropdown>
            </div>
        </nav>
    </header>
</template>

<script>
  import Vue from 'vue'
  import { mapGetters } from 'vuex'
  import { Dropdown } from 'bootstrap-vue/es/components'

  Vue.use(Dropdown)

  export default {
    data: () => ({
      appName: window.config.appName
    }),

    components: {
      Dropdown
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