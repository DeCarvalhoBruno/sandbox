<template>
    <div>
        <v-client-table :columns="columns" :data="table" :options="options" name="table">
            <a slot="uri" slot-scope="props" target="_blank" :href="props.row.uri"
               class="fa fa-eye"></a>
        </v-client-table>
    </div>
</template>

<script>
  import Vue from 'vue'
  import store from '~/store'
  import { mapGetters } from 'vuex'
  import { ClientTable, Event } from 'vue-tables-2'

  Vue.use(ClientTable, null, true, 'bootstrap4')

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'users',
    data () {
      return {
        columns: ['name', 'code', 'uri'],
        options: {
          headings: {
            name: 'Country Name',
            code: 'Country Code',
            uri: 'View Record'
          },
          sortable: ['name', 'code'],
          filterable: ['name', 'code']
        },
        methods: {}
      }
    },
    computed: mapGetters({
      table: 'table/table'
    }),
    created () {
    },
    beforeRouteEnter (to, from, next) {
      store.dispatch('table/fetchData', {
        entity: 'users'
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