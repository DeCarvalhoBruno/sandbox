<template>
    <div class="vue-dropzone dropzone file-upload w-100" :id="id" ref="dropzoneElement">
            <div class="file-upload-container" dz-clickable>
                <h3 class="dropfile-instructions">Déposez votre fichier ici (ou cliquez pour choisir)</h3>
                <p class="dropfile-instructions">Taille maximale : 2 Mo</p>
                <p class="dropfile-instructions">Formats acceptés : </p>
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
      id: {
        type: String,
        required: true
      },
      options: {
        type: Object,
        required: true
      },
      awss3: {
        type: Object,
        required: false,
        default: null
      },
      destroyDropzone: {
        type: Boolean,
        default: true,
        required: false
      }
    },
    data() {
      return {
        isS3: false,
        isS3OverridesServerPropagation: false,
        wasQueueAutoProcess: true,
      }
    },
    computed: {
      dropzoneSettings() {
        let defaultValues = {
          thumbnailWidth: 160,
          thumbnailHeight: 160,
          maxFilesize: 2, //in MB
          parallelUploads: 1,
          filesizeBase: 1024,
          previewTemplate: this.getPreviewTemplate(),
          autoQueue: true,
          previewsContainer: "#previews",
          clickable: ".file-upload-container",
          dictDefaultMessage: "",
          dictFallbackMessage: "",
          dictFallbackText: "",
          dictFileTooBig: "Ce fichier est trop volumineux ({{filesize}} Mo, maximum autorisé : {{maxFilesize}} Mo).",
          dictInvalidFileType: "Ce type de fichier n'est pas autorisé.",
          dictResponseError: "Le serveur a répondu avec un code {{statusCode}}.",
          dictCancelUpload: "Annuler l'envoi",
          dictCancelUploadConfirmation: "Confirmez-vous l'interruption de cet envoi ?",
          dictRemoveFile: "Enlever le fichier",
          dictRemoveFileConfirmation: null,
          dictMaxFilesExceeded: "Le nombre maximal de fichiers envoyables en une fois est atteint."
        }
        Object.keys(this.options).forEach(function(key) {
          defaultValues[key] = this.options[key]
        }, this)
        if (this.awss3 !== null) {
          defaultValues['autoProcessQueue'] = false;
          this.isS3 = true;
          this.isS3OverridesServerPropagation = (this.awss3.sendFileToServer === false);
          if (this.options.autoProcessQueue !== undefined)
            this.wasQueueAutoProcess = this.options.autoProcessQueue;

          if (this.isS3OverridesServerPropagation) {
            defaultValues['url'] = (files) => {
              return files[0].s3Url;
            }
          }
        }
        return defaultValues
      }
    },
    methods: {
      manuallyAddFile: function(file, fileUrl) {
        file.manuallyAdded = true;
        this.dropzone.emit("addedfile", file);
        fileUrl && this.dropzone.emit("thumbnail", file, fileUrl);

        var thumbnails = file.previewElement.querySelectorAll('[data-dz-thumbnail]');
        for (var i = 0; i < thumbnails.length; i++) {
          thumbnails[i].style.width = this.dropzoneSettings.thumbnailWidth + 'px';
          thumbnails[i].style.height = this.dropzoneSettings.thumbnailHeight + 'px';
          thumbnails[i].style['object-fit'] = 'contain';
        }
        this.dropzone.emit("complete", file)
        if (this.dropzone.options.maxFiles) this.dropzone.options.maxFiles--
        this.dropzone.files.push(file)
        this.$emit('vdropzone-file-added-manually', file)
      },
      setOption: function(option, value) {
        this.dropzone.options[option] = value
      },
      removeAllFiles: function(bool) {
        this.dropzone.removeAllFiles(bool)
      },
      processQueue: function() {
        let dropzoneEle = this.dropzone;
        if (this.isS3 && !this.wasQueueAutoProcess) {
          this.getQueuedFiles().forEach((file) => {
            this.getSignedAndUploadToS3(file);
          });
        } else {
          this.dropzone.processQueue();
        }
        this.dropzone.on("success", function() {
          dropzoneEle.options.autoProcessQueue = true
        });
        this.dropzone.on('queuecomplete', function() {
          dropzoneEle.options.autoProcessQueue = false
        })
      },
      init: function() {
        return this.dropzone.init();
      },
      destroy: function() {
        return this.dropzone.destroy();
      },
      updateTotalUploadProgress: function() {
        return this.dropzone.updateTotalUploadProgress();
      },
      getFallbackForm: function() {
        return this.dropzone.getFallbackForm();
      },
      getExistingFallback: function() {
        return this.dropzone.getExistingFallback();
      },
      setupEventListeners: function() {
        return this.dropzone.setupEventListeners();
      },
      removeEventListeners: function() {
        return this.dropzone.removeEventListeners();
      },
      disable: function() {
        return this.dropzone.disable();
      },
      enable: function() {
        return this.dropzone.enable();
      },
      filesize: function(size) {
        return this.dropzone.filesize(size);
      },
      accept: function(file, done) {
        return this.dropzone.accept(file, done);
      },
      addFile: function(file) {
        return this.dropzone.addFile(file);
      },
      removeFile: function(file) {
        this.dropzone.removeFile(file)
      },
      getAcceptedFiles: function() {
        return this.dropzone.getAcceptedFiles()
      },
      getRejectedFiles: function() {
        return this.dropzone.getRejectedFiles()
      },
      getFilesWithStatus: function() {
        return this.dropzone.getFilesWithStatus()
      },
      getQueuedFiles: function() {
        return this.dropzone.getQueuedFiles()
      },
      getUploadingFiles: function() {
        return this.dropzone.getUploadingFiles()
      },
      getAddedFiles: function() {
        return this.dropzone.getAddedFiles()
      },
      getActiveFiles: function() {
        return this.dropzone.getActiveFiles()
      },
      getSignedAndUploadToS3(file) {
        var promise = awsEndpoint.sendFile(file, this.awss3, this.isS3OverridesServerPropagation);
        if (!this.isS3OverridesServerPropagation) {
          promise.then((response) => {
            if (response.success) {
              file.s3ObjectLocation = response.message
              setTimeout(() => this.dropzone.processFile(file))
              this.$emit('vdropzone-s3-upload-success', response.message);
            } else {
              if ('undefined' !== typeof response.message) {
                this.$emit('vdropzone-s3-upload-error', response.message);
              } else {
                this.$emit('vdropzone-s3-upload-error', "Network Error : Could not send request to AWS. (Maybe CORS error)");
              }
            }
          });
        } else {
          promise.then(() => {
            setTimeout(() => this.dropzone.processFile(file))
          });
        }
        promise.catch((error) => {
          alert(error);
        });
      },
      setAWSSigningURL(location) {
        if (this.isS3) {
          this.awss3.signingURL = location;
        }
      },
      getPreviewTemplate(){
return `
<div class="table files previews-container">
    <div class="file-row template">
    <input type="hidden" class="row-id">
    <div class="container preview-row">
    <div class="row">

        <div class="col">
            <div class="preview"><img data-dz-thumbnail/></div>
            <p class="name" data-dz-name></p>
        </div>
        <div class="col preview-actions">
            <div class="row">
                <p class="size" data-dz-size></p>
            </div>
            <div class="row">
                <span data-dz-remove class="action-cancel-file">
                <i class="fa fa-cancel fa-2x"></i>
                </span>
                <button data-dz-remove type="button" title="Supprimer le média" class="action-delete-file btn btn-sm btn-danger">
                <svg aria-hidden="true" data-prefix="fas" data-icon="trash-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-trash-alt fa-w-14"><path fill="currentColor" d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z" class=""></path></svg>
                </button>
                <span class="action-edit-file" hidden="hidden" data-toggle="collapse"
                data-target=".div-media-update"
                aria-expanded="false" aria-controls="div-media-update" title="Modifier le média">
                <i class="fa fa-pencil fa-2x"></i>
                </span>
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0"
                aria-valuemax="100" aria-valuenow="0">
                <div class="progress-bar progress-bar-success" style="width:0%;"
                data-dz-uploadprogress></div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <div class="col-xs-12">
        <span class="dropjs-error clearfix error text-danger"></span>
    </div>
    <div class="col-xs-12 div-media-update collapse">
    </div>
    </div>
</div>
  `
      }
    },
    mounted() {
      if (this.$isServer && this.hasBeenMounted) {
        return
      }
      this.hasBeenMounted = true
      this.dropzone = new Dropzone(this.$refs.dropzoneElement, this.dropzoneSettings)
      let vm = this

      this.dropzone.on('thumbnail', function(file, dataUrl) {
        vm.$emit('vdropzone-thumbnail', file, dataUrl)
      })

      this.dropzone.on('addedfile', function(file) {
        if (vm.duplicateCheck) {
          if (this.files.length) {
            this.files.forEach(function(dzfile) {
              if (dzfile.name === file.name) {
                this.removeFile(file)
                vm.$emit('duplicate-file', file)
              }
            }, this)
          }
        }
        vm.$emit('vdropzone-file-added', file)
        if (vm.isS3 && vm.wasQueueAutoProcess) {
          vm.getSignedAndUploadToS3(file);
        }
      })

      this.dropzone.on('addedfiles', function(files) {
        vm.$emit('vdropzone-files-added', files)
      })

      this.dropzone.on('removedfile', function(file) {
        vm.$emit('vdropzone-removed-file', file)
        if (file.manuallyAdded) vm.dropzone.options.maxFiles++
      })

      this.dropzone.on('success', function(file, response) {
        vm.$emit('vdropzone-success', file, response)
        if (vm.isS3) {
          if(vm.isS3OverridesServerPropagation){
            var xmlResponse = (new window.DOMParser()).parseFromString(response, "text/xml");
            var s3ObjectLocation = xmlResponse.firstChild.children[0].innerHTML;
            vm.$emit('vdropzone-s3-upload-success', s3ObjectLocation);
          }
          if (vm.wasQueueAutoProcess)
            vm.setOption('autoProcessQueue', false);
        }
      })

      this.dropzone.on('successmultiple', function(file, response) {
        vm.$emit('vdropzone-success-multiple', file, response)
      })

      this.dropzone.on('error', function(file, message, xhr) {
        vm.$emit('vdropzone-error', file, message, xhr)
        if (this.isS3)
          vm.$emit('vdropzone-s3-upload-error');
      })

      this.dropzone.on('errormultiple', function(files, message, xhr) {
        vm.$emit('vdropzone-error-multiple', files, message, xhr)
      })

      this.dropzone.on('sending', function(file, xhr, formData) {
        if (vm.isS3) {
          if (vm.isS3OverridesServerPropagation) {
            let signature = file.s3Signature;
            Object.keys(signature).forEach(function (key) {
              formData.append(key, signature[key]);
            });
          } else {
            formData.append('s3ObjectLocation', file.s3ObjectLocation);
          }
        }
        vm.$emit('vdropzone-sending', file, xhr, formData)
      })

      this.dropzone.on('sendingmultiple', function(file, xhr, formData) {
        vm.$emit('vdropzone-sending-multiple', file, xhr, formData)
      })

      this.dropzone.on('complete', function(file) {
        vm.$emit('vdropzone-complete', file)
      })

      this.dropzone.on('completemultiple', function(files) {
        vm.$emit('vdropzone-complete-multiple', files)
      })

      this.dropzone.on('canceled', function(file) {
        vm.$emit('vdropzone-canceled', file)
      })

      this.dropzone.on('canceledmultiple', function(files) {
        vm.$emit('vdropzone-canceled-multiple', files)
      })

      this.dropzone.on('maxfilesreached', function(files) {
        vm.$emit('vdropzone-max-files-reached', files)
      })

      this.dropzone.on('maxfilesexceeded', function(file) {
        vm.$emit('vdropzone-max-files-exceeded', file)
      })

      this.dropzone.on('processing', function(file) {
        vm.$emit('vdropzone-processing', file)
      })

      this.dropzone.on('processing', function(file) {
        vm.$emit('vdropzone-processing', file)
      })

      this.dropzone.on('processingmultiple', function(files) {
        vm.$emit('vdropzone-processing-multiple', files)
      })

      this.dropzone.on('uploadprogress', function(file, progress, bytesSent) {
        vm.$emit('vdropzone-upload-progress', file, progress, bytesSent)
      })

      this.dropzone.on('totaluploadprogress', function(totaluploadprogress, totalBytes, totalBytesSent) {
        vm.$emit('vdropzone-total-upload-progress', totaluploadprogress, totalBytes, totalBytesSent)
      })

      this.dropzone.on('reset', function() {
        vm.$emit('vdropzone-reset')
      })

      this.dropzone.on('queuecomplete', function() {
        vm.$emit('vdropzone-queue-complete')
      })

      this.dropzone.on('drop', function(event) {
        vm.$emit('vdropzone-drop', event)
      })

      this.dropzone.on('dragstart', function(event) {
        vm.$emit('vdropzone-drag-start', event)
      })

      this.dropzone.on('dragend', function(event) {
        vm.$emit('vdropzone-drag-end', event)
      })

      this.dropzone.on('dragenter', function(event) {
        vm.$emit('vdropzone-drag-enter', event)
      })

      this.dropzone.on('dragover', function(event) {
        vm.$emit('vdropzone-drag-over', event)
      })

      this.dropzone.on('dragleave', function(event) {
        vm.$emit('vdropzone-drag-leave', event)
      })

      vm.$emit('vdropzone-mounted')
    },
    beforeDestroy() {
      if (this.destroyDropzone) this.dropzone.destroy()
    }
  }
</script>
