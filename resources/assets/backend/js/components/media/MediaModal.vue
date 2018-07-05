<template>
    <div>
        <b-modal v-model="modalShow"
                 size="lg"
                 @hidden="modalClose">
            <div slot="modal-header">
                Add Media
            </div>
            <div class="d-block text-center">


            </div>
            <div slot="modal-footer">
                <template v-if="true">
                    <button type="button" class="btn"
                    >Apply
                    </button>
                    <button
                            type="button"
                            class="btn btn-secondary"
                            @click="modalClose">{{$t('general.cancel')}}
                    </button>
                </template>
            </div>
        </b-modal>
    </div>
</template>
<script>
  import { Modal } from 'bootstrap-vue/es/components'

  export default {
    name: 'media-modal',
    components:{
      Modal,
    },
    props:{
      showModal:{required:true}
    },
    watch:{
      showModal(){
        this.modalShow = this.showModal
      }
    },
    data () {
      return {
        modalShow:false
      }
    },
    methods:{
      modalClose(){
        this.$emit('modal-close')
      }
    }
  }
</script>