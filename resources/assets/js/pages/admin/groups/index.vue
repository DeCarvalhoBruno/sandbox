<script>
  import Vue from 'vue'
  import store from '~/store'
  import Table from '~/components/table/table'

  Vue.use(Table)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'groups',
    components: {
      'v-table': Table
    },
    beforeRouteEnter (to, from, next) {
      store.dispatch('table/fetchData', {
        entity: 'groups',
        queryString: to.fullPath
      }).then(res => next())
    },
    render: function (h) {
      return h('div', {},
        [
          h(Table, {
            props: {
              entity: 'groups'
            }
          })])
    }
  }
</script>