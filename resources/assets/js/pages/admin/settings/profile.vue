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
        </div>
        <div class="form-group row">
            <b-card no-body class="w-100">
                <b-tabs card>
                    <b-tab :title="$t('pages.settings.avatar-tab')" active>
                        <p v-show="avatars.lenth>1" class="font-italic">Click on an avatar to apply it.</p>
                        <div class="avatar-group" :class="{'avatar-loading':ajaxIsLoading}">
                            <fa v-show="ajaxIsLoading" class="fa-5x sync-icon" icon="sync" spin></fa>
                            <ul class="p-0">
                                <li class="avatar-container"
                                    v-for="(avatar,index) in avatars"
                                    :key="index">
                                    <div class="avatar" :class="{'selected':avatar.used}"
                                         @click="setAvatarAsUsed(avatar.uuid,avatar.used)">
                                        <div class="avatar-inner">
                                            <img :src="`/media/users/image_avatar/${avatar.uuid}.${avatar.ext}`">
                                        </div>
                                    </div>

                                    <div class="avatar-controls">
                                        <button type="button" class="btn btn-sm"
                                                :class="{'btn-danger':!avatar.used,'disabled':avatar.used}"
                                                :title="$t('pages.settings.delete_avatar')"
                                                @click="deleteAvatar(avatar.uuid,avatar.used)">
                                            <fa icon="trash-alt"/>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </b-tab>
                    <b-tab :title="$t('pages.settings.avatar-ul-tab')" :disabled="avatars.length>6">
                        <wizard ref="wizard" :steps="steps" has-step-buttons="false"
                                :current-step-changed="currentStepChanged">
                            <div slot="s1">
                                <!--<dropzone :id="'dzone'"-->
                                <!--:options="{url:'/ajax/admin/media/add',autoProcessQueue: false}"-->
                                <!--:post-data="{-->
                                <!--type:'users',-->
                                <!--target:user.username,-->
                                <!--media:'image_avatar'}">-->
                                <!--</dropzone>-->
                                <dropzone class="dropzone"
                                          tag="section"
                                          v-bind="dropzoneOptions"
                                          ref="dropzone">
                                    <!--<div class="d-flex align-items-center justify-content-center w-100"-->
                                    <!--style="height:5vh; border-radius: 1rem;">-->
                                    <!--<button type="button" class="btn btn-primary"-->
                                    <!--@click="triggerBrowse">Upload Files-->
                                    <!--</button>-->
                                    <!--</div>-->
                                    <div class="dz-container" @click="triggerBrowse">
                                        <h4 class="dropfile-instructions">{{ $t('dropzone.choose_file')}}</h4>
                                        <p class="dropfile-instructions">{{ $t('dropzone.max_size')}}
                                            {{maxFilesize}}{{$t('units.MB')}}</p>
                                        <p class="dropfile-instructions">{{ $t('dropzone.accepted_formats')}} JPG,
                                            PNG</p>
                                        <fa class="fa-4x" icon="cloud-upload-alt"/>
                                    </div>
                                    <!-- Scoped slot -->
                                    <template slot="files" slot-scope="props">
                                        <div v-for="(file, i) in props.files" :key="file.id" :class="{'mt-5': i === 0}">
                                            <div class="table files previews-container">
                                                <div class="file-row template">
                                                    <div class="container position-relative">
                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="preview"><img v-show="file.dataUrl"
                                                                                          :src="file.dataUrl"
                                                                                          :alt="file.name"/></div>
                                                                <p class="name">{{file.name}}</p>
                                                            </div>
                                                            <div class="col preview-actions">
                                                                <div class="row preview-row">
                                                                    <p class="size">{{(file.size/1024/1024).toPrecision(3)}}&nbsp;{{$t('units.MB')}}</p>
                                                                </div>
                                                                <div class="row preview-row">
                                                                    <div id="dropzone_progress" class="progress">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                                                             role="progressbar" style="width: 100%"
                                                                             aria-valuenow="100" aria-valuemin="0"
                                                                             aria-valuemax="100"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="row preview-row">
                                                                    <pre></pre>
                                                                </div>
                                                            </div>
                                                            <div class="row button-crop-wrapper">
                                                                <button type="button"
                                                                        class="btn btn-lg btn-primary action-next-step"
                                                                @click="goToCropperStep">
                                                                    Proceed to cropping
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col">
                                                        <span class="dropzone-error clearfix text-danger"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </dropzone>
                            </div>
                            <div slot="s2">
                                <cropper :src="cropper_src" :crop-height="cropperCropHeight"
                                         :crop-width="cropperCropWidth" :filename="uploadedImageFilename"></cropper>
                            </div>
                            <div slot="s3">
                                <!--<dropzone2 :id="'dzone3'"-->
                                           <!--:options="{url:'/ajax/admin/media/add',autoProcessQueue: true}"-->
                                           <!--:image="imageToUpload"-->
                                           <!--:is-interactive="false"-->
                                           <!--:post-data="{-->
                                                <!--type:'users',-->
                                                <!--target:user.username,-->
                                                <!--media:'image_avatar'}">-->
                                <!--</dropzone2>-->
                            </div>
                        </wizard>
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
  // import Dropzone from '~/components/Dropzone'
  import { VueTransmit } from 'vue-transmit'
  import Wizard from '~/components/Wizard'
  import Cropper from '~/components/Cropper'
  import axios from 'axios'

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
      'dropzone': VueTransmit,
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
            label: 'Select',
            slot: 's1'
          },
          {
            label: 'Crop',
            slot: 's2'
          },
          {
            label: 'Upload',
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
        imageToUpload: null,
        currentStepChanged: true,
        userInfo: null,
        permissions: null,
        avatars: [],
        ajaxIsLoading: false,
        uploadedImageFilename: null,
        maxFilesize: 2,
        dropzoneOptions: {
          // createImageThumbnails:false,
          thumbnailWidth:2235,
          thumbnailHeight:1490,

          autoProcessQueue:false,
          acceptedFileTypes: ['image/*'],
          clickable: false,
          adapterOptions: {
            url: '/ajax/admin/media/add'
          }
        }
      }
    },
    computed: {
      ...mapGetters({
        user: 'auth/user'
      })
    },
    created () {

    },
    mounted () {
      let vm = this

      this.$refs.dropzone.$on('thumbnail', function(file,dataURI){
        vm.cropper_src = dataURI
        vm.cropperCropWidth = 128
        vm.cropperCropHeight = 128
        vm.uploadedImageFilename = file.name
        // console.log(file)
      })

      this.$refs.dropzone.$on('added-file', function(file){
        console.log(file)
        // var reader = new FileReader();
        // reader.readAsDataURL(file);
        // reader.onloadend = function () {
        //   console.log(reader.result);
        // };
      })


      /*
      this.$root.$on('dropzone_file_chosen', function (file) {
        vm.cropper_src = file.dataURL
        vm.cropperCropWidth = 128
        vm.cropperCropHeight = 128
        vm.currentStepChanged = !vm.currentStepChanged
        vm.uploadedImageFilename = file.name
      })

      this.$root.$on('cropper_cropped', function (data, filename) {
        vm.imageToUpload = {dataURI: data.toDataURL(), filename: filename}
        vm.currentStepChanged = !vm.currentStepChanged
      })

      this.$root.$on('dropzone_upload_complete', function () {
        axios.get('/ajax/admin/settings/avatar').then(({data}) => {
          vm.avatars = data
        })
      })
      */
    },
    methods: {
      goToCropperStep(){
        this.currentStepChanged = !this.currentStepChanged
      },
      triggerBrowse () {
        this.$refs.dropzone.triggerBrowseFiles()
      },
      deleteAvatar (uuid, alreadyUsed) {
        this.ajaxIsLoading = true
        if (!alreadyUsed) {
          axios.delete(`/ajax/admin/settings/avatar/${uuid}`).then(({data}) => {
            this.avatars = data
            this.ajaxIsLoading = false
          })
        }
      },
      setAvatarAsUsed (uuid, alreadyUsed) {
        if (!alreadyUsed) {
          this.ajaxIsLoading = true
          axios.patch('/ajax/admin/settings/avatar', {uuid: uuid}).then(({data}) => {
            this.avatars = data
            this.ajaxIsLoading = false
          })
        }
      },
      async update () {
        const {data} = await this.form.patch('/ajax/admin/settings/profile')
        this.$store.dispatch('auth/updateUser', {user: data})
        this.form.keys().forEach(key => {
          this.form[key] = this.userInfo[key]
        })
        this.$store.dispatch('session/setAlertMessageSuccess', this.$t('message.profile_updated'))
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
    }
  }
</script>
