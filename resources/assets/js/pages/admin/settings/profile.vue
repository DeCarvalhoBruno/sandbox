<template>
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
        <div class="form-group row">
            <label for="new_username" class="col-md-3 col-form-label">{{$t('db.new_username')}}</label>
            <div class="col-md-9">
                <input v-model="form.new_username" type="text"
                       name="new_username" id="new_username" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('new_username') }"
                       :placeholder="$t('db.new_username')"
                       aria-describedby="help_new_username">
                <has-error :form="form" field="new_username"/>
                <small id="help_new_username" class="text-muted">
                    {{$t('form.description.new_username',[form.username])}}
                </small>
            </div>
        </div>
        <div class="form-group row">
            <label for="first_name" class="col-md-3 col-form-label">{{$t('db.first_name')}}</label>
            <div class="col-md-9">
                <input v-model="form.first_name" type="text"
                       name="first_name" id="first_name" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('first_name') }"
                       :placeholder="$t('db.first_name')"
                       aria-describedby="help_first_name">
                <has-error :form="form" field="first_name"/>
                <small id="help_first_name" class="text-muted">{{$t('form.description.first_name')}}
                </small>
            </div>
        </div>
        <div class="form-group row">
            <label for="last_name" class="col-md-3 col-form-label">{{$t('db.last_name')}}</label>
            <div class="col-md-9">
                <input v-model="form.last_name" type="text"
                       name="last_name" id="last_name" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('last_name') }"
                       :placeholder="$t('db.last_name')"
                       aria-describedby="help_last_name">
                <has-error :form="form" field="last_name"/>
                <small id="help_last_name" class="text-muted">{{$t('form.description.last_name')}}
                </small>
            </div>
        </div>
        <div class="form-group row">
            <label for="new_email" class="col-md-3 col-form-label">{{$t('db.new_email')}}</label>
            <div class="col-md-9">
                <input v-model="form.new_email" type="text"
                       name="new_email" id="new_email" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('new_email') }"
                       :placeholder="$t('db.new_email')"
                       aria-describedby="help_new_email">
                <has-error :form="form" field="new_email"/>
                <small id="help_new_email" class="text-muted">
                    {{$t('form.description.new_email',[form.email])}}
                </small>
            </div>
        </div>
        <div class="form-group row">
            <b-card no-body class="w-100">
                <b-tabs card>
                    <b-tab :title="$t('pages.settings.avatar-tab')" active>
                        <dropzone :id="'dzone'"
                                  :options="{url:'/ajax/admin/media/add'}"
                                  :post-data="{
                                  type:'users',
                                  target:user.username,
                                  media:'image_avatar'}"></dropzone>
                    </b-tab>

                    <b-tab :title="$t('pages.settings.avatar-ul-tab')">
                        Tab Contents 2
                    </b-tab>
                </b-tabs>
            </b-card>
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
  import Dropzone from '~/components/Dropzone'
  import { Form, HasError, AlertForm } from '~/components/form'
  import { mapGetters } from 'vuex'
  import { Modal, Tabs, Card } from 'bootstrap-vue/es/components'

  Vue.use(Modal)
  Vue.use(Tabs)
  Vue.use(Card)

  export default {
    scrollToTop: false,
    components: {
      'v-button': Button,
      HasError,
      AlertForm,
      Modal,
      Tabs,
      Card,
      Dropzone
    },
    metaInfo () {
      return {title: this.$t('general.settings')}
    },
    data: () => ({
      form: new Form({
        username: '',
        first_name: '',
        last_name: '',
        email: ''
      })
    }),
    computed: mapGetters({
      user: 'auth/user'
    }),
    created () {
      this.form.keys().forEach(key => {
        this.form[key] = this.user[key]
      })
    },
    mounted () {
      // this.$on('dropzone.success',function(){
      //
      // })
    },
    methods: {
      async update () {
        const {data} = await this.form.patch('/ajax/admin/settings/profile')
        this.$store.dispatch('auth/updateUser', {user: data})
        this.form.keys().forEach(key => {
          this.form[key] = this.user[key]
        })
        this.$store.dispatch('session/setAlertMessageSuccess', this.$t('message.profile_updated'))
      }
    }
  }
</script>
