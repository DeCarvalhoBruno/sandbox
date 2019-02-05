<template>
    <div>
        <v-table :entity="entity" :data="computedTable"
                 select-column-name="group_name">
            <template #body-action="props">
                <td>
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
            </template>
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
          total: 0
        }
      }
    },
    mixins: [
      TableMixin
    ],
    methods: {
      async deleteRow (data) {
        this.swalDeleteWarning(
          this.$t('modal.group_delete.h'),
          this.$tc('modal.group_delete.t', 1, {name: data.group_name}),
          this.$t('general.delete')
        ).then(async (result) => {
          if (result.value) {
            await axios.delete(`/ajax/admin/groups/${data.group_name}`)
            this.refreshTableData()
            this.swalNotification('success', this.$tc('message.group_delete_ok', 1, {name: data.group_name}))
          }
        })
      }
    },
    metaInfo () {
      return {title: this.$t('title.group_index')}
    }
  }
</script>