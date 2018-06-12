<template>
    <div class="dz-file-upload">
        <form :id="id" ref="dropzone">
            <input type="hidden" :value="postData.type" name="type">
            <input type="hidden" :value="postData.target" name="target">
            <input type="hidden" :value="postData.media" name="media">
            <input type="hidden" :value="postData.group" name="group">
            <input type="hidden" :value="postData.category" name="category">
        </form>
        <div class="dz-container" v-show="isInteractive" dz-clickable>
            <h4 class="dropfile-instructions">{{ $t('dropzone.choose_file')}}</h4>
            <p class="dropfile-instructions">{{ $t('dropzone.max_size')}} {{maxFilesize}}{{$t('units.MB')}}</p>
            <p class="dropfile-instructions">{{ $t('dropzone.accepted_formats')}} JPG, PNG</p>
            <fa class="fa-4x" icon="cloud-upload-alt"/>
        </div>
        <div :id="['previews'+id]" class="row">
        </div>
    </div>
</template>

<script>
  // import awsEndpoint from '../services/urlsigner'
  import Dropzone from 'dropzone' //eslint-disable-line
  Dropzone.autoDiscover = false
  export default {
    name: 'dropzone',
    props: {
      id: String,
      options: Object,
      postData: Object,
      image: null,
      isInteractive: {
        type: Boolean,
        default: true
      }
    },
    data () {
      return {
        maxFilesize: 2,
        fileCounter: 0,
        defaultValues: {
          thumbnailWidth: 160,
          thumbnailHeight: 160,
          maxFilesize: 2, //in MB
          parallelUploads: 1,
          filesizeBase: 1024,
          autoQueue: true,
          previewsContainer: '#previews' + this.id,
          clickable: '.dz-container',
          dictDefaultMessage: '',
          dictFallbackMessage: '',
          dictFallbackText: '',
          dictFileTooBig: this.$t('dropzone.file_too_big'),
          dictInvalidFileType: this.$t('dropzone.invalid_type'),
          dictResponseError: this.$t('dropzone.response_error'),
          dictCancelUpload: this.$t('dropzone.cancel_upload'),
          dictCancelUploadConfirmation: this.$t('dropzone.cancel_confirm'),
          dictMaxFilesExceeded: this.$t('dropzone.max_files_exceeded')
        }
      }
    },
    mounted () {
      let vm = this

      Object.keys(this.options).forEach(function (key) {
        this.defaultValues[key] = this.options[key]
      }, this)

      this.defaultValues.previewTemplate = this.getPreviewTemplate(
        this.defaultValues.autoProcessQueue && this.defaultValues.autoProcessQueue == true
      )

      this.dropzone = new Dropzone(this.$refs.dropzone, this.defaultValues)

      this.dropzone.on('thumbnail', function (file) {
        if (file.status != 'error') {
          let nextStepSelector = file.previewElement.querySelector('.action-next-step')
          if (nextStepSelector) {
            nextStepSelector.addEventListener('click', function () {
              vm.$root.$emit('dropzone_file_chosen', file)
            })
          }
        }
      })
      if (this.image) {
        this.addFileFromDataUrl(this.image)
      }

      this.dropzone.on('success', function (file, response) {
        vm.$root.$emit('dropzone_upload_complete')
        document.querySelector('#dropzone_progress').style.opacity = '0'
      })

      this.dropzone.on('sending', function (file) {
        // Show the total progress bar when upload starts
        document.querySelector('#dropzone_progress').style.opacity = '1'
      })

      this.dropzone.on('error', function (file, message) {
        if (vm.defaultValues.autoProcessQueue)
          return
        let selector = file.previewElement.querySelector('.dropzone-error')
        selector.innerHTML = '<strong>' + message + '</strong>'
        selector.className += ' error'
        file.previewElement.querySelector('.action-next-step').className = 'd-none'
      })
    },
    beforeDestroy () {
      this.dropzone.destroy()
    },
    methods: {
      dataURItoFile (data) {
        let byteString, mimestring

        if (data.dataURI.split(',')[0].indexOf('base64') !== -1) {
          byteString = atob(data.dataURI.split(',')[1])
        } else {
          byteString = decodeURI(data.dataURI.split(',')[1])
        }

        mimestring = data.dataURI.split(',')[0].split(':')[1].split(';')[0]

        let content = new Array()
        for (let i = 0; i < byteString.length; i++) {
          content[i] = byteString.charCodeAt(i)
        }

        let blob = new Blob([new Uint8Array(content)], {type: mimestring})
        let parts = [blob, new ArrayBuffer()]

        return new File(parts, data.filename, {
          lastModified: new Date(0) // optional - default = now
        })

      },
      addFileFromDataUrl: function (data) {
        let dz = this.dropzone
        let blob = this.dataURItoFile(data)
        blob.upload = {
          progress: 0,
          total: blob.size,
          bytesSent: 0
        }
        dz.files.push(blob)
        blob.status = Dropzone.ADDED
        dz.emit('addedfile', blob)
        dz.emit('thumbnail', blob, data.dataURI)
        dz._enqueueThumbnail(blob)
        return dz.accept(blob, function (error) {
          if (error) {
            blob.accepted = false
            dz._errorProcessing([blob], error)
          } else {
            blob.accepted = true
            dz.enqueueFile(blob)
          }
          return dz._updateMaxFilesReachedClass()
        })
      },
      addFile (file) {
        let dz = this.dropzone

        dz.files.push(file)
        file.status = Dropzone.ADDED
        dz.emit('addedfile', file)
        dz.emit('thumbnail', file, file.dataURL)

        return dz.accept(file, function (error) {
          if (error) {
            file.accepted = false
            dz._errorProcessing([file], error)
          } else {
            file.accepted = true
            dz.enqueueFile(file)
          }
          return dz._updateMaxFilesReachedClass()
        })
      },
      getPreviewTemplate (autoProcessQueue) {
        return `
<div class="table files previews-container">
    <div class="file-row template">
        <div class="container position-relative">
            <div class="row">
                <div class="col">
                    <div class="preview"><img data-dz-thumbnail/></div>
                    <p class="name" data-dz-name></p>
                </div>
                <div class="col preview-actions">
                    <div class="row preview-row">
                        <p class="size" data-dz-size></p>
                    </div>
                    <div class="row preview-row">
                        <div id="dropzone_progress" class="progress">
                          <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
                        </div>
                    </div>
                    <div class="row preview-row">
                    <div>${autoProcessQueue ? 'true' : 'false'}</div>
                    </div>
                </div>
                <div class="row button-crop-wrapper">
                  <button type="button" class="btn btn-lg btn-primary action-next-step">Proceed to cropping</button>
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
  `
      }
    }
  }
</script>
