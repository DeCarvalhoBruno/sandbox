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
        <div id="previews" class="row">
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
      images: Array,
      isInteractive: {
        type: Boolean,
        default: true
      }
    },
    data () {
      return {
        maxFilesize: 2,
        fileCounter:0
      }
    },
    computed: {
      dropzoneSettings () {
        let defaultValues = {
          thumbnailWidth: 160,
          thumbnailHeight: 160,
          maxFilesize: 2, //in MB
          parallelUploads: 1,
          filesizeBase: 1024,
          previewTemplate: this.getPreviewTemplate(),
          autoQueue: true,
          previewsContainer: '#previews',
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
        Object.keys(this.options).forEach(function (key) {
          defaultValues[key] = this.options[key]
        }, this)
        return defaultValues
      },
    },
    mounted () {
      let vm = this
      this.dropzone = new Dropzone(this.$refs.dropzone, this.dropzoneSettings)

      this.dropzone.on('thumbnail', function (file) {
        if (file.status != 'error') {
          // let rowId = file.previewElement.querySelector('.row-id')
          // rowId.setAttribute('value',file.upload.uuid)
          // vm.$root.$emit('dropzone_thumbnail_created', file)
          file.previewElement.querySelector('.action-next-step').addEventListener('click',function(){
            vm.$root.$emit('dropzone_file_chosen', file)
          })
        }
      })
      // console.log(this.images.length,this.images)
      if (this.images && this.images.length > 0) {
        this.manuallyAddFile(this.images[0])
        // console.log(this.dropzone.getAcceptedFiles())
      }

      this.dropzone.on('success', function (file, response) {
        // console.log(file)

        // vm.$root.$emit('dropzone_file_uploaded', {...file, ...response})
      })

      //
      this.dropzone.on('error', function (file, message) {
        let selector = file.previewElement.querySelector('.dropzone-error')
        selector.innerHTML = '<strong>' + message + '</strong>'
        selector.className += ' error'
        file.previewElement.querySelector('.action-next-step').className = 'd-none'
      })

      this.dropzone.on('removedfile', function (file, message) {

      })
    },
    beforeDestroy () {
      this.dropzone.destroy()
    },
    methods: {
      dataURItoFile (dataURI) {
        let byteString, mimestring

        if (dataURI.split(',')[0].indexOf('base64') !== -1) {
          byteString = atob(dataURI.split(',')[1])
        } else {
          byteString = decodeURI(dataURI.split(',')[1])
        }

        mimestring = dataURI.split(',')[0].split(':')[1].split(';')[0]

        let content = new Array()
        for (let i = 0; i < byteString.length; i++) {
          content[i] = byteString.charCodeAt(i)
        }

        let blob = new Blob([new Uint8Array(content)], {type: mimestring})
        let parts = [blob, new ArrayBuffer()]

        return new File(parts, 'image.jpg', {
          lastModified: new Date(0) // optional - default = now
        })

      },
      manuallyAddFile: function (dataUrl) {
        let dz = this.dropzone

        let blob = this.dataURItoFile(dataUrl)

        blob.upload = {
          progress: 0,
          total: blob.size,
          bytesSent: 0
        }

        dz.files.push(blob)
        blob.status = Dropzone.ADDED
        dz.emit('addedfile', blob)
        dz.emit('thumbnail', blob, dataUrl)
        // dz._enqueueThumbnail(blob)

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
        /*
        file.manuallyAdded = true
        this.dropzone.emit('addedfile', file)
        this.dropzone.emit('thumbnail', file, dataUrl)

        var thumbnails = file.previewElement.querySelectorAll('[data-dz-thumbnail]')
        for (var i = 0; i < thumbnails.length; i++) {
          thumbnails[i].style.width = this.dropzoneSettings.thumbnailWidth + 'px'
          thumbnails[i].style.height = this.dropzoneSettings.thumbnailHeight + 'px'
          thumbnails[i].style['object-fit'] = 'contain'
        }
        this.dropzone.emit('complete', file)
        if (this.dropzone.options.maxFiles) this.dropzone.options.maxFiles--
        this.dropzone.files.push(file)
        this.$emit('dropzone-file-added-manually', file)
        */
      },
      getPreviewTemplate () {
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
                        <span data-dz-remove class="action-cancel-file">
                        <i class="fa fa-cancel fa-2x"></i>
                        </span>
                        <button data-dz-remove type="button" title="${this.$t('dropzone.delete_media')}" class="action-delete-file btn btn-sm btn-danger">
                        <svg aria-hidden="true" data-prefix="fas" data-icon="trash-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-trash-alt fa-w-14"><path fill="currentColor" d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z" class=""></path></svg>
                        </button>
                        <button type="button" class="btn btn-sm action-edit-file" hidden="hidden" data-toggle="collapse" data-target=".div-media-update" aria-expanded="false" aria-controls="div-media-update" title="${this.$t(
              'dropzone.edit_media')}"><svg aria-hidden="true" data-prefix="fas" data-icon="pencil-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-pencil-alt fa-w-16"><path fill="currentColor" d="M497.9 142.1l-46.1 46.1c-4.7 4.7-12.3 4.7-17 0l-111-111c-4.7-4.7-4.7-12.3 0-17l46.1-46.1c18.7-18.7 49.1-18.7 67.9 0l60.1 60.1c18.8 18.7 18.8 49.1 0 67.9zM284.2 99.8L21.6 362.4.4 483.9c-2.9 16.4 11.4 30.6 27.8 27.8l121.5-21.3 262.6-262.6c4.7-4.7 4.7-12.3 0-17l-111-111c-4.8-4.7-12.4-4.7-17.1 0zM124.1 339.9c-5.5-5.5-5.5-14.3 0-19.8l154-154c5.5-5.5 14.3-5.5 19.8 0s5.5 14.3 0 19.8l-154 154c-5.5 5.5-14.3 5.5-19.8 0zM88 424h48v36.3l-64.5 11.3-31.1-31.1L51.7 376H88v48z" class=""></path></svg></button>
                    </div>
                    <div class="row preview-row">
                        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0"
                        aria-valuemax="100" aria-valuenow="0">
                            <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="button" class="btn btn-lg btn-primary action-next-step">Use image</button>
                        <input type="hidden" class="row-id" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
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
