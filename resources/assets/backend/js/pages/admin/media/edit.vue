<template>
    <div>
        
    </div>
</template>

<script>
  import axios from 'axios'
  import { Form, HasError, AlertForm } from '~/components/form'

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'media-edit',
    data () {
      return {
        form: new Form(),
        mediaInfo: {},

      }    },
    methods:{
      getInfo (data) {
        //this.form = new Form(data)
      },
    },
    beforeRouteEnter (to, from, next) {
      axios.get(`/ajax/admin/media/${to.params.media}`).then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>