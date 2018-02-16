<template>
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card">
                <div class="card-header">
                    {{ $t('pages.auth.reset_password') }}
                </div>

                <div class="card-body">
                    <form @submit.prevent="send" @keydown="form.onKeydown($event)">
                        <alert-form :form="form" :message="status"/>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('general.email') }}</label>
                            <div class="col-md-7">
                                <input ref="email" v-model="form.email" type="email" name="email" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"/>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group row">
                            <div class="col-md-9 ml-md-auto">
                                <v-button :loading="form.busy">
                                    {{ $t('pages.auth.send_password_reset_link') }}
                                </v-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Button from '~/components/Button'
  import { Form, HasError, AlertForm } from '~/components/form'

  export default {
    middleware: 'guest',
    components: {
      HasError, AlertForm, 'v-button': Button
    },

    metaInfo () {
      return {title: this.$t('pages.auth.reset_password')}
    },

    data: () => ({
      status: '',
      form: new Form({
        email: ''
      })
    }),

    mounted () {
      this.$refs.email.focus()
    },

    methods: {
      async send () {
        const {data} = await this.form.post('/admin/password/email')

        this.status = data.status

        this.form.reset()
      }
    }
  }
</script>
