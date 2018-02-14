<template>
    <div class="row">
        <div class="col-lg-8 m-auto">
            <card :title="$t('pages.auth.reset_password')">
                <form @submit.prevent="validateForm" @keydown="form.onKeydown($event)">
                    <alert-success :form="form" :message="status"/>

                    <!-- Email -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('general.email') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.email" type="email" name="email" class="form-control"
                                   :class="{ 'is-invalid': form.errors.has('email') }" readonly>
                            <has-error :form="form" field="email"/>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('general.password') }}</label>
                        <div class="col-md-7">
                            <input ref="password" v-model="form.password" type="password" name="password"
                                   class="form-control"
                                   :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"/>
                        </div>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ $t('pages.auth.confirm_password') }}</label>
                        <div class="col-md-7">
                            <input v-model="form.password_confirmation" type="password" name="password_confirmation"
                                   class="form-control"
                                   :class="{ 'is-invalid': form.errors.has('password_confirmation') }">
                            <has-error :form="form" field="password_confirmation"/>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group row">
                        <div class="col-md-9 ml-md-auto">
                            <v-button :loading="form.busy">
                                {{ $t('pages.auth.reset_password') }}
                            </v-button>
                        </div>
                    </div>
                </form>
            </card>
        </div>
    </div>
</template>

<script>
  import Form from 'vform'
  import swal from 'sweetalert2'

  export default {
    middleware: 'guest',

    metaInfo () {
      return {title: this.$t('pages.auth.reset_password')}
    },

    data: () => ({
      status: '',
      form: new Form({
        token: '',
        email: '',
        password: '',
        password_confirmation: ''
      })
    }),

    created () {
      this.form.email = this.$route.query.email
      this.form.token = this.$route.params.token
    },

    mounted () {
      this.$refs.password.focus()
    },

    methods: {
      validateForm () {
        if (this.form.password === this.form.password_confirmation) {
          this.reset()
        } else {
          this.form.errors.set(
            {
              'password': this.$t('error.passwords_dont_match'),
              'password_confirmation': this.$t('error.passwords_dont_match')
            })

        }
      },
      async reset () {
        const {data} = await this.form.post('/admin/password/reset')

        this.status = data.status

        swal({
          type: 'success',
          title: this.$t('general.success'),
          text: data.status,
          reverseButtons: true,
          confirmButtonText: this.$t('general.ok'),
        }).then(() => {
          this.$router.push({name: 'admin.login'})
        })
      }
    }
  }
</script>
