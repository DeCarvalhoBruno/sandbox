<template>
    <div class="media-resize">
        <ol class="resize-instructions">
            <li class="format-select-step">

            </li>
            <li><h3>Redimensionnez l'image</h3>
                <div class="container p-0">
                        <div class="cropper row">
                            <img ref="img" class="cropper-img" :src="src"/>
                        </div>
                        <div class="cropper-commands row">
                            <div class="container p-0">
                                <div class="row">
                                    <div class="col align-self-center">

                                    <button type="button" :title="$t('general.reload')"
                                            class="btn btn-primary cropper-reset" @click="cropper.reset()">
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
            </li>
            <li><h3>Prévisualisez le résultat</h3>
                <div class="col-xs-12 container-fluid">
                    <div class="cropper-preview-wrapper">
                        <div class="cropper-preview">
                        </div>
                    </div>
                </div>
            </li>
            <li><h3>Appliquez les changements</h3>
                <div class="row">
                    <button id="btn_resize" class="btn btn-primary" type="button">
                        Recadrer
                    </button>
                </div>
            </li>
        </ol>
    </div>
</template>

<script>
    import Cropper from 'cropperjs'
  export default {
    name: 'cropper',
    props:{
      src:String,
      cropHeight:Number,
      cropWidth:Number,
    },
    data () {
      return {}
    },
    mounted(){
          let vm=this
      this.cropper = new Cropper(this.$refs.img, {
        preview: ".cropper-preview",
        dragMode: "move",
        viewMode: 1,
        cropBoxResizable:false,

        ready: function () {
          this.cropper.setCropBoxData({width:vm.cropWidth,height:vm.cropHeight})



          //Object { left: 23.302013422818817, top: 0, width: 449.39597315436237, height: 300, naturalWidth: 2232, naturalHeight: 1490 }
          // var data = processedImage.cropper('getImageData');
          // processedImage.cropper('clear');
          //$('.img-width').html(Math.round(data.naturalWidth));
          //$('.img-height').html(Math.round(data.naturalHeight));

        }
      });
    }
  }
</script>