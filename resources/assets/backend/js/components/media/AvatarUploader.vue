<template>
    <div class="form-group row">
        <div class="card w-100">
            <b-tabs card>
                <b-tab :title="$t('pages.settings.avatar-tab')" @click="avatarTabClicked" active>
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
                            :current-step-parent="currentStep">
                        <div slot="s1">
                            <keep-alive>
                                <dropzone class="dropzone"
                                          tag="section"
                                          v-bind="dropzoneOptions"
                                          :adapter-options="{
                                          url: '/ajax/admin/media/add',
                                            params:{
                                                type:'users',
                                                target:user.username,
                                                media:'image_avatar'
                                            }
                                            }"
                                          ref="dropzone">
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
                                        <div v-for="(file, i) in props.files" :key="file.id"
                                             :class="{'mt-5': i === 0}">
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
                                                                    <p class="size">
                                                                        {{(file.size/1024/1024).toPrecision(3)}}&nbsp;{{$t('units.MB')}}</p>
                                                                </div>
                                                                <div class="row preview-row">
                                                                    <div id="dropzone_progress" class="progress">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                                             role="progressbar"
                                                                             :style="`width: ${file.upload.progress}%`"
                                                                             :aria-valuenow="file.upload.progress"
                                                                             aria-valuemin="0"
                                                                             aria-valuemax="100"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row button-crop-wrapper" v-show="uploadSuccess">
                                                                <button type="button"
                                                                        class="btn btn-lg btn-primary action-next-step"
                                                                        @click="currentStep=1">
                                                                    Proceed to cropping
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col">
                                                        <span class="dropzone-error clearfix text-danger" v-html="error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </dropzone>
                            </keep-alive>
                        </div>
                        <div slot="s2">
                            <cropper :src="cropper_src" :crop-height="minCropBoxHeight"
                                     :crop-width="minCropBoxWidth" :filename="uploadedImageFilename"></cropper>
                        </div>
                        <div slot="s3">
                            <div class="container p-0 mt-2">
                                <div class="row justify-content-lg-center">
                                    <div class="col col-lg-6 text-center">
                                        <img :src="croppedImageData.dataURI"/>
                                    </div>
                                </div>
                                <div class="row justify-content-lg-center mt-3">
                                    <div class="col col-lg-6 text-center">
                                        <p class="blinker blinker-red" v-if="ajaxIsLoading">Processing in
                                            progress...</p>
                                        <p v-else>The avatar has been processed, you can return to the avatar
                                            tab.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </wizard>
                </b-tab>
            </b-tabs>
        </div>
    </div>
</template>
<script>
  import Vue from 'vue'
  import { VueTransmit } from 'vue-transmit'
  import Wizard from '~/components/Wizard'
  import Cropper from '~/components/Cropper'
  import axios from 'axios'

  import { Form, HasError, AlertForm } from '~/components/form'
  import { Tabs } from 'bootstrap-vue/es/components'

  Vue.use(Tabs)

  export default {
    name: 'avatar-uploader',
    components: {
      Tabs,
      'dropzone': VueTransmit,
      Wizard,
      Cropper
    },
    props: {
      user: Object,
      avatarsParent: Array
    },
    computed: {
      avatars () {
        return this.avatarsParent
      }
    },
    data () {
      return {
        steps: [
          {
            label: 'Upload',
            slot: 's1'
          },
          {
            label: 'Crop',
            slot: 's2'
          },
          {
            label: 'Review',
            slot: 's3'
          }
        ],
        cropper_src: null,
        minCropBoxHeight: 0,
        minCropBoxWidth: 0,
        imageToUpload: null,
        currentStep: 0,
        ajaxIsLoading: false,
        uploadedImageFilename: null,
        maxFilesize: 2,
        croppedImageData: Object,
        error:'',
        dropzoneOptions: {
          // createImageThumbnails:false,
          // thumbnailWidth:3000,
          // thumbnailHeight:3000,
          // autoProcessQueue:false,
          acceptedFileTypes: ['image/jpg', 'image/jpeg', 'image/png'],
          clickable: false
        },
        avatarSubmitted: false,
        uploadSuccess:false
      }
    },
    mounted () {
      let vm = this
      this.$root.$on('cropper_cropped', function (cp, croppedCanvas) {
        vm.croppedImageData.dataURI = croppedCanvas.toDataURL()
        vm.currentStep = 2
        vm.ajaxIsLoading = true

        //{ x: 521.5, y: 149, width: 1192, height: 1192, rotate: 0, scaleX: 1, scaleY: 1 }

        axios.post('/ajax/admin/media/crop',
          {uuid: vm.croppedImageData.filename, height: cp.height, width: cp.width, x: cp.x, y: cp.y})
          .then(({data}) => {
            vm.$root.$emit('avatars_updated', data)
            vm.ajaxIsLoading = false
          })
          .catch(e => {
            vm.ajaxIsLoading = false
          })
      })
      this.$root.$on('wizard_step_reset', function () {
        vm.currentStep = 0
      })
    },
    methods: {
      avatarTabClicked () {
        //After a successful avatar upload, currentStep=2
        // the user can click on the avatar tab in which case we need to reset current step
        // so a new avatar can be uploaded
        if (this.currentStep === 2)
          this.currentStep = 0
      },
      triggerBrowse () {
        let vm = this
        this.uploadSuccess=false
        this.$refs.dropzone.triggerBrowseFiles()
        this.$refs.dropzone.$on('success', function (file, response) {
          vm.cropper_src = `/media/tmp/${response.filename}`
          vm.croppedImageData.filename = response.filename
          vm.minCropBoxHeight = 128
          vm.minCropBoxWidth = 128
          vm.uploadSuccess=true
        })
        this.$refs.dropzone.$on('error', function (file, error) {
          vm.error = error
        })
      },
      deleteAvatar (uuid, alreadyUsed) {
        if (!alreadyUsed) {
          this.ajaxIsLoading = true
          axios.delete(`/ajax/admin/settings/avatar/${uuid}`).then(({data}) => {
            this.$root.$emit('avatars_updated', data)
            this.ajaxIsLoading = false
          })
        }
      },
      setAvatarAsUsed (uuid, alreadyUsed) {
        if (!alreadyUsed) {
          this.ajaxIsLoading = true
          axios.patch('/ajax/admin/settings/avatar', {uuid: uuid}).then(({data}) => {
            this.$root.$emit('avatars_updated', data)
            this.ajaxIsLoading = false
          })
        }
      }
    }
  }
</script>