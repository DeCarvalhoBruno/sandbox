<template>
    <vue-transmit ref="uploader"
                  upload-area-classes="vh-20"
                  drag-class="dragging"
                  v-bind="options"
                  @added-file="onAddedFile"
                  @success="onUploadSuccess"
                  @success-multiple="onUploadSuccessMulti"
                  @timeout="onError"
                  @error="onError">
        <flex-col align-v="center"
                  class="h-100">
            <flex-row align-h="center">
                <b-btn variant="primary"
                       @click="triggerBrowse"
                       class="w-50">
                    Upload Files
                </b-btn>
            </flex-row>
        </flex-col>
        <template slot-scope="{ uploadingFiles }"
                  slot="files">
            <flex-row v-for="file in uploadingFiles"
                      :key="file.id"
                      align-v="center"
                      no-wrap
                      class="w-100 my-5"
                      style="height: 100px;">
                <img v-show="file.dataUrl"
                     :src="file.dataUrl"
                     :alt="file.name"
                     class="img-fluid w-25">
                <b-progress :value="file.upload.progress"
                            show-progress
                            :precision="2"
                            :variant="file.upload.progress === 100 ? 'success' : 'warning'"
                            :animated="file.upload.progress === 100"
                            class="ml-2 w-100"></b-progress>
            </flex-row>
        </template>
    </vue-transmit>
    <b-col v-for="file in files"
           :key="file.id"
           cols="4">
        <b-card :title="file.name"
                :sub-title="file.type"
                :img-src="file.src"
                :img-alt="file.name"
                img-top>
            <pre>{{ file | json }}</pre>
        </b-card>
    </b-col>
    <b-modal v-model="showModal"
             title="File Upload: Error">
        <p class="bg-danger text-white p-3 my-2"
           v-html="error"></p>
    </b-modal>
</template>

<script>
  import { VueTransmit } from 'vue-transmit'

  export default {
    name: 'App',
    components: {
      'vue-transmit': VueTransmit
    },
    data () {
      return {
        options: {
          acceptedFileTypes: ['image/*'],
          clickable: false,
          accept: this.accept,
          uploadMultiple: true,
          maxConcurrentUploads: 4,
          adapterOptions: {
            url: '/',
            timeout: 3000,
            errUploadError: xhr => xhr.response.message
          }
        },
        files: [],
        showModal: false,
        error: '',
        count: 0
      }
    },
    methods: {
      triggerBrowse () {
        this.$refs.uploader.triggerBrowseFiles()
      },
      onAddedFile (file) {
        console.log(
          this.$refs.uploader.inputEl.value,
          this.$refs.uploader.inputEl.files
        )
      },
      onUploadSuccess (file, res) {
        console.log(res)
        if (!this.options.uploadMultiple) {
          file.src = res.url[0]
          this.files.push(file)
        }
      },
      onUploadSuccessMulti (files, res) {
        console.log(...arguments)
        for (let i = 0; i < files.length; i++) {
          files[i].src = res.url[i]
          this.files.push(files[i])
        }
      },
      onError (file, errorMsg) {
        this.error = errorMsg
        this.showModal = true
      },
      listen (event) {
        this.$refs.uploader.$on(event, (...args) => {
          console.log(event)
          for (let arg of args) {
            // console.log(`${typeof arg}: ${JSON.stringify(arg, undefined, 2)}`)
            console.log(arg)
          }
        })
      },
      accept (file, done) {
        this.count++
        console.log(JSON.stringify(file, undefined, 2))
        done()
      }
    },
    filters: {
      json (value) {
        return JSON.stringify(value, null, 2)
      }
    },
    mounted () {
      [
        'drop',
        'drag-start',
        'drag-end',
        'drag-enter',
        'drag-over',
        'drag-leave',
        'accepted-file',
        'rejected-file',
        'accept-complete',
        'added-file',
        'added-files',
        'removed-file',
        'thumbnail',
        'error',
        'error-multiple',
        'processing',
        'processing-multiple',
        'upload-progress',
        'total-upload-progress',
        'sending',
        'sending-multiple',
        'success',
        'success-multiple',
        'canceled',
        'canceled-multiple',
        'complete',
        'complete-multiple',
        'reset',
        'max-files-exceeded',
        'max-files-reached',
        'queue-complete'
      ].forEach(this.listen)
    }
  }
</script>

<style>
    #app {
        font-family: "Avenir", Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 60px;
    }
</style>
