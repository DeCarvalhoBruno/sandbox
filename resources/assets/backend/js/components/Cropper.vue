<template>
    <div class="media-resize">
        <div class="resize-instructions container">
            <div class="row">
                <h5 class="text-center">{{$t('media.cropper_resize_image')}}</h5>
                <div class="container p-0">
                    <div class="cropper row">
                        <img ref="img" class="cropper-img" :src="src"/>
                    </div>
                    <div class="cropper-commands row">
                        <div class="container p-0">
                            <div class="row">
                                <div class="col align-self-center">
                                    <button type="button" :title="$t('general.reload')"
                                            class="btn btn-primary cropper-reset" @click="resetCropper()">
                                        <fa icon="sync-alt"/>
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
                        <div class="cropper-preview">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="container">
                    <div class="row">
                        <div class="col align-self-center">
                            <button class="btn btn-primary" @click="crop()" type="button"
                            >{{$t('media.cropper_crop_upload')}}
                            </button>
                            <button class="btn btn-primary btn-light" @click="cancel()" type="button"
                            >{{$t('general.cancel')}}
                            </button>
                        </div>
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
      src: String,
      cropHeight: Number,
      cropWidth: Number,
      filename:String
    },
    data () {
      return {
        cropper: Object
      }
    },
    mounted () {
      let vm = this
      this.cropper = new Cropper(this.$refs.img, {
        preview: '.cropper-preview',
        dragMode: 'move',
        viewMode: 1,
        cropBoxResizable: true,
        zoomable:false,

        ready: function () {
          vm.setCropboxDimensions()
        }
      })
    },
    methods: {
      resetCropper () {
        this.cropper.reset()
        this.setCropboxDimensions()
      },
      setCropboxDimensions () {
        this.cropper.setAspectRatio(1)
      },
      crop(){
        this.$root.$emit(
          'cropper_cropped',
          this.cropper.getData(true),
          this.cropper.getCroppedCanvas({imageSmoothingQuality:'high',width:128,height:128}),
        )
      },
      cancel(){
        this.$root.$emit(
          'wizard_step_reset',
        )
      },

    }
  }
</script>