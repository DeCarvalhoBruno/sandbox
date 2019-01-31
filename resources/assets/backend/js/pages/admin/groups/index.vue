<template>
    <div>
        <v-table :entity="entity" :data="computedTable"
                 :is-multi-select="true"
                 select-column-name="group_name">
            <td slot="body-action" slot-scope="props">
                <div class="inline">
                    <template v-if="props.row.group_name">
                        <router-link :to="{
                        name: 'admin.groups.edit',
                        params: { group: props.row.group_name }
                        }">
                            <button class="btn btn-sm btn-info">
                                <fa icon="pencil-alt">
                                </fa>
                            </button>
                        </router-link>
                    </template>
                    <button class="btn btn-sm btn-danger"
                            @click="deleteRow(props.row)">
                        <fa icon="trash-alt">
                        </fa>
                    </button>
                    <template v-if="props.row.group_name">
                        <router-link
                                :to="{ name: 'admin.groups.members', params: { group: props.row.group_name }}">
                            <button class="btn btn-sm btn-success">
                                <fa icon="users">
                                </fa>
                            </button>
                        </router-link>
                    </template>
                </div>
            </td>
        </v-table>
    </div>
</template>

<script>
  import Vue from 'vue'
  import Table from '~/components/table/table'
  import axios from 'axios'
  import TableMixin from '~/mixins/tables'
  Vue.use(Table)

  export default {
    name: 'groups',
    layout: 'basic',
    middleware: 'check-auth',
    components: {
      'v-table': Table
    },
    data: function () {
      return {
        entity: 'groups',
        data: {
          total: 0,
        },
      }
    },
    mixins: [
      TableMixin
    ],
    methods: {
      async deleteRow (data) {
        try {
          await axios.delete(`/ajax/admin/groups/${data.group_name}`)
          this.$store.dispatch(
            'session/setAlertMessageSuccess',
            this.$t('message.group_delete_ok', {group: data.group_name})
          )
          this.$store.dispatch('table/fetchData', {
            entity: this.entity,
            queryString: this.$route.fullPath
          })
        } catch (e) {}
      }
    },
  }
</script>