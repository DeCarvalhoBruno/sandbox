<template>
    <div class="card">
        <div class="card-header">
            {{ $t('pages.auth.your_password') }}
        </div>

        <div class="card-body">
            <form @submit.prevent="update" @keydown="form.onKeydown($event)">
                <alert-form :form="form" :message="$t('message.password_updated')"/>
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
                        <v-button type="success" :loading="form.busy">{{ $t('general.update') }}</v-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
  import Button from '~/components/Button'
  import { Form, HasError, AlertForm } from '~/components/form'

  export default {
    scrollToTop: false,
    components: {
      'v-button': Button,
      HasError, AlertForm
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
        await this.form.patch('/api/settings/password')

        this.form.reset()
      }
    }
  }
</script>
