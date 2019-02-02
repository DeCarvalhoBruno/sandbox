<template>
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
        <alert-form :form="form" :dismiss-label="$t('general.close')"></alert-form>
        <div class="form-group row">
            <label for="new_username" class="col-md-3 col-form-label">{{$t('db.new_username')}}</label>
            <div class="col-md-9">
                <input v-model="form.fields.new_username" type="text" autocomplete="off"
                       name="new_username" id="new_username" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('new_username') }"
                       :placeholder="$t('db.new_username')"
                       aria-describedby="help_new_username">
                <has-error :form="form" field="new_username"></has-error>
                <small id="help_new_username" class="text-muted">
                    {{$t('form.description.new_username',[form.fields.username])}}
                </small>
            </div>
        </div>
        <div class="form-group row">
            <label for="first_name" class="col-md-3 col-form-label">{{$t('db.first_name')}}</label>
            <div class="col-md-9">
                <input v-model="form.fields.first_name" type="text" autocomplete="off"
                       name="first_name" id="first_name" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('first_name') }"
                       :placeholder="$t('db.first_name')"
                       aria-describedby="help_first_name">
                <has-error :form="form" field="first_name"></has-error>
                <small id="help_first_name" class="text-muted">{{$t('form.description.first_name')}}
                </small>
            </div>
        </div>
        <div class="form-group row">
            <label for="last_name" class="col-md-3 col-form-label">{{$t('db.last_name')}}</label>
            <div class="col-md-9">
                <input v-model="form.fields.last_name" type="text" autocomplete="off"
                       name="last_name" id="last_name" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('last_name') }"
                       :placeholder="$t('db.last_name')"
                       aria-describedby="help_last_name">
                <has-error :form="form" field="last_name"></has-error>
                <small id="help_last_name" class="text-muted">{{$t('form.description.last_name')}}
                </small>
            </div>
        </div>
        <div class="form-group row">
            <label for="new_email" class="col-md-3 col-form-label">{{$t('db.new_email')}}</label>
            <div class="col-md-9">
                <input v-model="form.fields.new_email" type="text"
                       name="new_email" id="new_email" class="form-control"
                       :class="{ 'is-invalid': form.errors.has('new_email') }"
                       :placeholder="$t('db.new_email')"
                       aria-describedby="help_new_email">
                <has-error :form="form" field="new_email"></has-error>
                <small id="help_new_email" class="text-muted">
                    {{$t('form.description.new_email',[form.fields.email])}}
                </small>
            </div>
        </div>
        <div class="form-group row">
            <div class="container col-lg-6 justify-content-center">
                <div class="col-lg text-center">
                    <v-button :loading="form.busy">{{ $t('general.update') }}</v-button>
                </div>
            </div>
        </div>
        <avatar-uploader :user="user" :avatars-parent="avatars"/>
    </form>
</template>

<script>
  import Button from '~/components/Button'
  import AvatarUploader from '~/components/media/AvatarUploader'
  import axios from 'axios'

  import { Form, HasError, AlertForm } from '~/components/form'
  import { mapGetters } from 'vuex'

  export default {
    scrollToTop: false,
    components: {
      'v-button': Button,
      HasError,
      AlertForm,
      AvatarUploader
    },
    metaInfo () {
      return {title: this.$t('general.settings')}
    },
    data () {
      return {
        form: new Form({
          username: '',
          first_name: '',
          last_name: '',
          email: ''
        }),
        userInfo: null,
        permissions: null,
        avatars: []
      }
    },
    computed: {
      ...mapGetters({
        user: 'auth/user'
      })
    },
    mounted () {
      this.$root.$on('avatars_updated', avatars => {
        this.avatars = avatars
      })
    },
    methods: {
      async update () {
        const {data} = await this.form.patch('/ajax/admin/settings/profile')
        this.$store.dispatch('auth/updateUser', {user: data})
        this.form.keys().forEach(key => {
          this.form[key] = this.userInfo[key]
        })
        this.swalNotification('success', this.$t('message.profile_updated'))
      },
      getInfo (data) {
        this.form = new Form(data.user)
        this.userInfo = data.user
        this.permissions = data.permissions
        this.avatars = data.avatars
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/users/session`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    },
    beforeDestroy(){
      this.$root.$off('avatars_updated')
    }
  }
</script>
