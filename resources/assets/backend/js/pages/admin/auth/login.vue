<template>
    <div class="row">
        <div class="col-lg-5 m-auto">
            <div class="card">
                <div class="card-body p-5">
                    <alert-form :form="form" :show-errors="false"/>
                    <form @submit.prevent="login" @keydown="form.onKeydown($event)">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('general.email') }}</label>
                            <div class="col-md-9">
                                <input v-model="form.fields.email" type="email" name="email" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">{{ $t('general.password') }}</label>
                            <div class="col-md-9">
                                <input v-model="form.fields.password" type="password" name="password" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3"></div>
                            <div class="col-md-9 d-flex">
                                <checkbox v-model="remember" name="remember">
                                    {{ $t('pages.auth.remember_me') }}
                                </checkbox>

                                <a href="/password/reset" class="small ml-auto my-auto">{{ $t('pages.auth.forgot_password') }}</a>
                                <!--<router-link :to="{ name: 'admin.password.request' }" class="small ml-auto my-auto">-->
                                    <!--{{ $t('pages.auth.forgot_password') }}-->
                                <!--</router-link>-->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-7 offset-md-3 d-flex">
                                <v-button :loading="form.busy">
                                    {{ $t('general.login') }}
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
  import Checkbox from '~/components/Checkbox'
  import { Form, HasError, AlertForm } from '~/components/form'

  export default {
    middleware: 'guest',
    metaInfo () {
      return {title: this.$t('title.login')}
    },
    components: {
      'v-button': Button,
      Checkbox,
      HasError,
      AlertForm,
    },
    data: () => ({
      form: new Form({
        email: '',
        password: ''
      }),
      remember: false,
    }),

    methods: {
      async login () {
        try {
          const {data} = await this.form.post('/admin/login')
          this.$store.dispatch('auth/updateUser', {user: data.user})
          this.$store.dispatch('auth/saveToken', {
            token: data.token,
            remember: this.remember
          })
        } catch (e) {

        }
        this.$router.push({name: 'admin.dashboard'})
      }
    }
  }
</script>
