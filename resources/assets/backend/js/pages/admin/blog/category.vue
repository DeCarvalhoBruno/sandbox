<template>
    <div>
        <tree-list></tree-list>
    </div>
</template>

<script>
  import TreeList from '~/components/tree-list/TreeList'

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'category',
    components:{
      TreeList
    },
    data () {
      return {}
    }
  }
</script>