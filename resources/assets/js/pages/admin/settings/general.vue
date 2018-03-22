<template>
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
        <!--<alert-form :form="form" :message="$t('message.password_updated')"/>-->
        <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.auth.new_password') }}</label>
            <div class="col-md-7">
                <select class="custom-select" v-model="form.locale">
                    <!--<option disabled value="">{{$t('pages.users.filter_group')}}</option>-->
                    <option v-for="(locale,idx) in locales" :key="idx">{{locale}}</option>
                </select>
            </div>

        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.auth.confirm_password') }}</label>
            <div class="col-md-7">

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
  import Vue from 'vue'
  import { mapGetters } from 'vuex'
  import Button from '~/components/Button'
  import { Form, HasError, AlertForm } from '~/components/form'

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
    data: () => ({
      form: new Form({
        locale: '',
      })
    }),
    methods: {
      async update () {
        await this.form.patch('/ajax/admin/settings/password')
        this.form.reset()
        this.$store.dispatch('session/setAlertMessageSuccess', this.$t('message.password_updated'))
      }
    }
  }
</script>
