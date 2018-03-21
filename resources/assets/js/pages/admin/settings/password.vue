<template>
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
        <!--<alert-form :form="form" :message="$t('message.password_updated')"/>-->
        <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.auth.new_password') }}</label>
            <div class="col-md-7">
                <input v-model="form.password" type="password" name="password" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('password') }">
                <has-error :form="form" field="password"/>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.auth.confirm_password') }}</label>
            <div class="col-md-7">
                <input v-model="form.password_confirmation" type="password" name="password_confirmation"
                       class="form-control"
                       :class="{ 'is-invalid': form.errors.has('password_confirmation') }">
                <has-error :form="form" field="password_confirmation"/>
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
  import Button from '~/components/Button'
  import { Form, HasError, AlertForm } from '~/components/form'
  import { Card, Nav } from 'bootstrap-vue/es/components'

  Vue.use(Card)

  export default {
    scrollToTop: false,
    components: {
      'v-button': Button,
      HasError,
      AlertForm,
      Card
    },
    metaInfo () {
      return {title: this.$t('general.settings')}
    },

    data: () => ({
      form: new Form({
        password: '',
        password_confirmation: ''
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
