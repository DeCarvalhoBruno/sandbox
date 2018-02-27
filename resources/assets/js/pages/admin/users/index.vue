<template>
    <div>
        <v-table :entity="'users'">
            <th slot="header-action">
                {{$t('general.actions')}}
            </th>
            <td slot="body-action" slot-scope="props">
                <div class="inline">
                    <router-link :to="{ name: 'admin.users.edit', params: { user: props.row.username } }">
                        <button class="btn btn-sm btn-info">
                            <fa icon="pencil-alt">
                            </fa>
                        </button>
                    </router-link>
                    <router-link :to="{ name: 'admin.dashboard' }">
                        <button class="btn btn-sm btn-danger">
                            <fa icon="trash-alt">
                            </fa>
                        </button>
                    </router-link>
                </div>
            </td>
        </v-table>
    </div>
</template>

<script>
  import Vue from 'vue'
  import store from '~/store'
  import Table from '~/components/table/table'

  Vue.use(Table)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'users',
    components: {
      'v-table': Table
    },
    beforeRouteEnter (to, from, next) {
      store.dispatch('table/fetchData', {
        entity: 'users',
        queryString: to.fullPath
      }).then(res => next())
    }
  }
</script>