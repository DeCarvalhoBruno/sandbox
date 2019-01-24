<template>
    <div class="media-resize" ref="imageContainer">
        <div class="resize-instructions container">
            <div class="row">
                <h5 class="text-center">{{$t('media.cropper_resize_image')}}</h5>
                <div class="container p-0">
                    <div class="cropper row">
                        <img ref="img" class="cropper-img"/>
                    </div>
                    <div class="cropper-commands row">
                        <div class="container p-0">
                            <div class="row">
                                <div class="col align-self-center">
                                    <button type="button" :title="$t('general.reload')"
                                            class="btn btn-primary cropper-reset" @click="resetCropper()">
                                        <fa icon="sync-alt"></fa>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row">
                            <p class="cropper-errors text-danger"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <h5 class="text-center">{{$t('media.cropper_preview')}}</h5>
                <div class="col-xs-12 container-fluid">
                    <div class="cropper-preview-wrapper">
                        <div class="cropper-preview" ref="cropperPreview">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="container">
                    <div class="row">
                        <slot name="cropper-actions">
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Cropper from 'cropperjs'

  export default {
    name: 'cropper',
    props: {
      src: {required: true},
      containerWidth: {required: true, type: Number},
      cropHeight: {type: Number, default: 0},
      cropWidth: {type: Number, default: 0},
      cropperActive:false
    },
    data () {
      return {
        cropper: Object
      }
    },
    watch: {
      cropperActive(){
        this.makeCropper()
      },
      src () {
        this.$refs.img.setAttribute('src', this.src)
      }
    },
    mounted () {
      if (typeof this.src === 'string') {
        this.$refs.img.setAttribute('src', this.src)
        this.makeCropper()
      }
    },
    updated () {

    },
    methods: {
      makeCropper () {
        let vm = this
        let containerWidth = Math.round(this.containerWidth * 60 / 100)
        let containerHeight = Math.round(this.containerWidth * 50 / 100)
        this.cropper = new Cropper(this.$refs.img, {
          preview: '.cropper-preview',
          dragMode: 'move',
          viewMode: 1,
          minCanvasWidth:containerWidth-50,
          minCanvasHeight:containerHeight-50,
          minContainerWidth: containerWidth,
          minContainerHeight: containerHeight,
          cropBoxResizable: true,
          zoomable: true,
          ready: function () {

            vm.setCropboxDimensions()
          }
        })
      },
      resetCropper () {
        this.cropper.reset()
        this.setCropboxDimensions()
      },
      setCropboxDimensions () {
        this.cropper.setAspectRatio(1)
      },
      crop () {
        this.$root.$emit(
          'cropper_cropped',
          this.cropper.getData(true),
          this.cropper.getCroppedCanvas({imageSmoothingQuality: 'high', width: 128, height: 128})
        )
      },
      cancel () {
        this.$root.$emit(
          'wizard_step_reset'
        )
      }
    }
  }
</script>