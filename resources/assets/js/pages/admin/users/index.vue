<script>
  import Vue from 'vue'
  import store from '~/store'
  import { mapGetters } from 'vuex'
  import Table from '~/components/table/table'

  Vue.use(Table)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'users',
    components: {
      'v-table': Table
    },

    computed: mapGetters({
      rows: 'table/table',
      columns: 'table/columns',
      sortableColumns: 'table/sortableColumns'
    }),

    beforeRouteEnter (to, from, next) {
      store.dispatch('table/fetchData', {
        entity: 'users',
        queryString: to.fullPath
      }).then(res => next())
    },

    render: function (h) {
      return h('div', {},
        [
          h(Table, {
            props: {
              rows: this.rows,
              columns: this.columns,
              'sortable-columns': this.sortableColumns,
              entity: 'users'
            }
          })])
    }
  }
</script>