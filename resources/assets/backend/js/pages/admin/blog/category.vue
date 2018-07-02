<template>
    <div>
        <tree-list :data="data"></tree-list>
    </div>
</template>

<script>
  import TreeList from '~/components/tree-list/TreeList'
  import axios from 'axios'

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'category',
    components: {
      TreeList
    },
    data () {
      return {
        data: Array
      }
    },
    methods: {
      getInfo (data) {
        this.data = data
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get('/ajax/admin/blog/categories').then(({data}) => {
        next(vm => vm.getInfo(data))
      })
    }
  }
</script>