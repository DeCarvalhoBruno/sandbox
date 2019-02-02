<template>
<div class="card">
  <div class="card-body">
      <div class="tree-list-container container p-0 m-0">
          <div v-if="error" class="row">
              <p class="text-danger">{{error}}</p>
          </div>
          <div class="row">
              <tree-list :add-root-button-label="$t('pages.blog.add_root_button')"
                         :data="data"
                         @has-errored="displayError"></tree-list>
          </div>
      </div>
  </div>
</div>
</template>

<script>
  import TreeList from '~/components/tree-list/TreeList'
  import axios from 'axios'

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'blog-category',
    components: {
      TreeList
    },
    data () {
      return {
        data: [],
        error:''
      }
    },
    methods: {
      displayError(error){
        this.error=error
      },
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