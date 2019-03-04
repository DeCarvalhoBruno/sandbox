<template>
  <form @submit.prevent="update" @keydown="form.onKeydown($event)">
    <div class="form-group row">
      <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.user.language') }}</label>
      <div class="col-md-7">
        <select class="custom-select" v-model="form.fields.locale">
          <option v-for="(locale,idx) in locales" :key="idx" :value="locale">{{$t('locales.'+locale)}}
          </option>
        </select>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-md-3 col-form-label text-md-right"></label>
      <div class="col-md-7">
        <p>{{$t('pages.user.notifications')}}</p>
        <div v-for="event in settings.events" :key="'events'+event.id"
             class="custom-control custom-switch m-1">
          <input type="checkbox"
                 class="custom-control-input" :value="event.id" :id="event.id"
                 v-model="form.fields.events">
          <label class="custom-control-label" :for="event.id">{{event.name}}</label>
        </div>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-md-9 ml-md-auto">
        <submit-button :loading="form.busy">{{ $t('general.update') }}</submit-button>
      </div>
    </div>
  </form>
</template>

<script>
  import axios from 'axios'
  import { mapGetters } from 'vuex'
  import SubmitButton from 'back_path/components/SubmitButton'
  import { Form } from 'back_path/components/form'
  import routesI18n from 'back_path/lang/routes'
  import swal from 'back_path/mixins/sweet-alert'

  export default {
    name:'settings-general',
    scrollToTop: false,
    components: {
      SubmitButton
    },
    mixins: [
      swal
    ],
    computed: mapGetters({
      locale: 'prefs/locale',
      locales: 'prefs/locales'
    }),
    metaInfo () {
      return {title: this.$t('title.settings')}
    },
    data: function () {
      return {
        settings: {events: [], settings: []},
        form: new Form({
          locale: this.$store.getters['prefs/locale'],
          events: []
        })
      }
    },
    methods: {
      async update () {
        this.form.busy = true
        let locale = this.form.fields.locale
        if (locale !== this.locale) {
          this.$store.dispatch('prefs/setLocale', {locale})
          let prefix = ''
          if (locale !== this.$store.getters['prefs/fallback']) {
            prefix += '/' + locale
          }
          window.history.pushState('', '', '/' + routesI18n[locale]['admin.user.general'])
          this.$router.go(1)
          this.$router.go()
        }
        await this.form.patch('/ajax/admin/user/general')
        this.$store.dispatch('auth/patchUser', this.form.fields.events.join(','))
        this.$store.dispatch('broadcast/updateNotifications', {
            data: {
              token: this.$store.getters['auth/token'],
              user: this.$store.getters['auth/user'],
              events: this.form.fields.events
            }
          }
        )
        this.swalNotification('success', this.$t('message.settings_updated'))
        this.form.busy = false
      },
      getInfo (data) {
        this.settings = data
        if (data.existing.events == null) {
          data.existing.events = []
        } else {
          this.form.fields.events = data.existing.events.split(',')
        }
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get('/ajax/admin/user/general').then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>
