<template>
    <div>
        <dropzone class="dropzone"
                  tag="section"
                  v-bind="dropzoneOptions"
                  :adapter-options="{
                      url: '/ajax/admin/media/add',
                        params:{
                            type:'blog',
                            target:this.blogPostSlug,
                            media:'image_featured'
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
    </div>
</template>

<script>
  import Vue from 'vue'
  import { VueTransmit } from 'vue-transmit'

  export default {
    name: 'blog-image-uploader',
    components: {
      'dropzone': VueTransmit,
    },
    props: {
      blogPostSlug: {required: true}
    },
    data () {
      return {
        maxFilesize: 2,
        dropzoneOptions: {
          // createImageThumbnails:false,
          // thumbnailWidth:3000,
          // thumbnailHeight:3000,
          // autoProcessQueue:false,
          acceptedFileTypes: ['image/jpg', 'image/jpeg', 'image/png'],
          clickable: false
        }
      }
    },
    methods: {
      triggerBrowse () {
        let vm = this
        this.$refs.dropzone.triggerBrowseFiles()
        this.$refs.dropzone.$on('success', function (file, response) {

        })
        this.$refs.dropzone.$on('error', function (file, error) {
          vm.error = error
        })
      }
    }
  }
</script>