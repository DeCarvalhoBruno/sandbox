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
                <span class="navbar-toggler-icon"></span>
            </button>
            <div v-if="user" class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <span><a class="nav-link sidebar-toggle" data-toggle="push-menu" role="button"></a></span>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto">
                    <!-- Authenticated -->
                    <li v-if="user" class="nav-item dropdown mr-5">
                        <a class="nav-link dropdown-toggle text-light"
                           href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ user.username }}
                        </a>
                        <div class="dropdown-menu">
                            <router-link :to="{ name: 'admin.settings.profile' }" class="dropdown-item pl-3">
                                <fa icon="cog" fixed-width/>
                                {{ $t('general.settings') }}
                            </router-link>

                            <div class="dropdown-divider"></div>
                            <a @click.prevent="logout" class="dropdown-item pl-3"  href="#">
                                <fa icon="sign-out-alt" fixed-width/>
                                {{ $t('general.logout') }}
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>

<script>
  import { mapGetters } from 'vuex'
  import LocaleDropdown from './LocaleDropdown'

  export default {
    data: () => ({
      appName: window.config.appName,
    }),

    computed: mapGetters({
      user: 'auth/user',
    }),

    components: {
      LocaleDropdown
    },

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