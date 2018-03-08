<template>
    <div>
        <v-table :entity="'users'" :is-multi-select="true" :rows="rows">
            <th slot="header-select-all">
                <div class="form-check">
                    <input class="form-check-input position-static"
                           type="checkbox"
                           :aria-label="$t('general.select_all')"
                           :title="$t('general.select_all')"
                           @click="toggleSelectAll">
                </div>
            </th>
            <th slot="header-action">
                {{$t('general.actions')}}
            </th>
            <td slot="body-select-row" slot-scope="props">
                <div class="form-check">
                    <input class="form-check-input position-static"
                           type="checkbox"
                           :aria-label="$t('tables.select_item',{name:props.row[$t('db_raw_reverse.full_name')]})"
                           :title="$t('tables.select_item',{name:props.row[$t('db_raw_reverse.full_name')]})"
                           v-model="props.row.selected">
                </div>
            </td>
            <td slot="body-action" slot-scope="props">
                <div class="inline">
                    <router-link :to="{ name: 'admin.users.edit', params: { user: props.row.username } }">
                        <button type="button" class="btn btn-sm btn-info"
                                :title="$t('tables.edit_item',{name:props.row[$t('db_raw_reverse.full_name')]})">
                            <fa icon="pencil-alt">
                            </fa>
                        </button>
                    </router-link>
                    <button type="button" class="btn btn-sm btn-danger"
                            :title="$t('tables.delete_item',{name:props.row[$t('db_raw_reverse.full_name')]})"
                            @click="deleteRow(props.row)">
                        <fa icon="trash-alt">
                        </fa>
                    </button>
                </div>
            </td>
        </v-table>
    </div>
</template>

<script>
  import Vue from 'vue'
  import store from '~/store'
  import Table from '~/components/table/table'
  import { mapGetters } from 'vuex'
  import axios from 'axios'

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
        allSelected: false
      }
    },
    computed: {
      ...mapGetters({
        rows: 'table/rows'
      })
    },
    methods: {
      toggleSelectAll () {
        this.allSelected = !this.allSelected
        this.rows.forEach(row => {
          if (this.allSelected) {
            row.selected = true
          } else {
            row.selected = false
          }
        })
      },
      deleteRows (data) {
        console.log(this.getSelectedRows())
      },
      async deleteRow (data) {
        await axios.delete(`/ajax/admin/users/${data.username}`)
        this.$store.dispatch('session/setMessageSuccess', this.$t('message.user_delete_ok'))
        this.$store.dispatch('table/fetchData', {
          entity: 'users',
          queryString: this.$route.fullPath
        })
      },
      getSelectedRows () {
        let selected = []
        this.rows.forEach(row => {
          if (row.selected) {
            selected.push(row)
          }
        })
        return selected
      }
    },
    beforeRouteEnter (to, from, next) {
      store.dispatch('table/fetchData', {
        entity: 'users',
        queryString: to.fullPath
      }).then(res => next())
    }
  }
</script>