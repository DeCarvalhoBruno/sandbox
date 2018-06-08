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
                        <wizard ref="wizard" :steps="steps" has-step-buttons="false" :current-step-parent="currentStep">
                            <div slot="s1">
                                <dropzone :id="'dzone'"
                                          :options="{url:'/ajax/admin/media/add',autoProcessQueue: false}"
                                          :post-data="{
                                type:'users',
                                target:user.username,
                                media:'image_avatar'}">
                                </dropzone>
                            </div>
                            <div slot="s2">
                                <cropper :src="cropper_src" :crop-height="cropperCropHeight"
                                         :crop-width="cropperCropWidth"></cropper>
                            </div>
                            <div slot="s3">
                                <dropzone :id="'dzone3'"
                                          :options="{url:'/ajax/admin/media/add',autoProcessQueue: true}"
                                          :images="imagesToUpload"
                                          :is-interactive="false"
                                          :post-data="{
                                                type:'users',
                                                target:user.username,
                                                media:'image_avatar'}">
                                </dropzone>
                            </div>
                        </wizard>
                    </b-tab>
                    <b-tab :title="$t('pages.settings.avatar-ul-tab')">


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
  import Wizard from '~/components/Wizard'
  import Cropper from '~/components/Cropper'

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
      Dropzone,
      Wizard,
      Cropper
    },
    metaInfo () {
      return {title: this.$t('general.settings')}
    },
    data () {
      return {
        steps: [
          {
            label: 'Upload',
            slot: 's1'
          },
          {
            label: 'Edit',
            slot: 's2'
          },
          {
            label: 'Review',
            slot: 's3'
          }
        ],
        form: new Form({
          username: '',
          first_name: '',
          last_name: '',
          email: ''
        }),
        cropper_src: null,
        cropperCropHeight: 0,
        cropperCropWidth: 0,
        imagesToUpload: null,
        currentStep:true
      }
    },
    computed: {
      ...mapGetters({
                   user: 'auth/user'
                 }),
      // nextStep(){
      //   console.log(this.currentStep)
      //   return this.currentStep++
      // }
    },
    created () {
      this.form.keys().forEach(key => {
        this.form[key] = this.user[key]
      })

    },
    mounted () {
      let vm = this
      this.$root.$on('dropzone_file_uploaded', function (data) {
        vm.cropper_src = data.dataURL
        let dimensions = data.dimensions.split('x')
        vm.cropperCropWidth = parseInt(dimensions[0])
        vm.cropperCropHeight = parseInt(dimensions[1])
        delete(data.dataURL)
      })
      this.$root.$on('dropzone_thumbnail_created', function (file) {
        vm.imagesToUpload = file
        vm.cropper_src = file.dataURL
        vm.cropperCropWidth = 128
        vm.cropperCropHeight = 128
      })
      this.$root.$on('dropzone_file_chosen', function (file) {
        vm.imagesToUpload = file
        vm.cropper_src = file.dataURL
        vm.cropperCropWidth = 128
        vm.cropperCropHeight = 128
        // console.log(file.name)
        vm.currentStep=!vm.currentStep
      })
    },

    methods: {
      wizardNextStep(){
        return true;
      },
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
