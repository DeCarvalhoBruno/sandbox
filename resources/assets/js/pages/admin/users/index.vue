<template>
    <div>
        <v-table :rows="rows" :columns="columns" :sortable-columns="sortableColumns" :entity="'users'">

        </v-table>
    </div>
</template>

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
    data: function () {
      return {
      }
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

      // next(vm => {
      //   store.dispatch('table/fetchData', {
      //     entity: vm.$i18n.t('route.users')
      //   })
      // })

      // getPost(to.params.id, (err, post) => {
      //   next(vm => vm.setData(err, post))
      // })
    }
  }

</script>