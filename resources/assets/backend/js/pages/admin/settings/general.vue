<template>
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
        <!--<alert-form :form="form" :message="$t('message.password_updated')"/>-->
        <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.settings.language') }}</label>
            <div class="col-md-7">
                <select class="custom-select" v-model="form.locale">
                    <!--<option disabled value="">{{$t('pages.users.filter_group')}}</option>-->
                    <option v-for="(locale,idx) in locales" :key="idx" :value="locale">{{$t('locales.'+locale)}}
                    </option>
                </select>
            </div>

        </div>
        <div class="form-group row">
            <div class="col-md-9 ml-md-auto">
                <v-button :loading="form.busy">{{ $t('general.update') }}</v-button>
            </div>
        </div>
    </form>
</template>

<script>
  import { mapGetters } from 'vuex'
  import Button from '~/components/Button'
  import { Form, HasError, AlertForm } from '~/components/form'
  import routesI18n from '~/lang/routes'

  export default {
    scrollToTop: false,
    components: {
      'v-button': Button,
      HasError,
      AlertForm
    },
    computed: mapGetters({
      locale: 'lang/locale',
      locales: 'lang/locales'
    }),
    metaInfo () {
      return {title: this.$t('general.settings')}
    },
    data: function () {
      return {
        form: new Form({
          locale: this.$store.getters['lang/locale']
        })
      }
    },
    methods: {
      update () {
        let locale = this.form.locale
        if (locale !== this.locale) {
          this.$store.dispatch('lang/setLocale', {locale})
          let prefix = ''
          if (locale !== this.$store.getters['lang/fallback']) {
            prefix += '/' + locale
          }
          window.history.pushState('', '', prefix + '/' + routesI18n[locale]['admin.settings.general'])
          this.$router.go(1)
          this.$router.go()
          this.$store.dispatch('session/setAlertMessageSuccess', this.$t('message.profile_updated'))
        }

      }
    }
  }
</script>
