<template>
    <div class="container">
        <div class="row">
            <div class="container">
                <div class="row py-2">
                    <div class="col-md-8">
                        <div class="form-row align-items-center">
                            <div class="col my-1">
                                <!--<label class="sr-only" for="select_apply_to_all"></label>-->
                                <select class="custom-select mr-sm-2" id="select_apply_to_all" v-model="selectApply">
                                    <option disabled value="">{{$t('tables.grouped_actions')}}</option>
                                    <option value="del">{{$t('tables.option_del_user')}}</option>
                                </select>
                            </div>
                            <div class="col my-1">
                                <button type="button" class="btn btn-primary"
                                        :title="$t('tables.btn_apply_title')"
                                        @click="applyToSelected">
                                    {{$t('general.apply')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <span class="float-right mt-3">{{total}}&nbsp;{{$tc('db.user',total)}}</span>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <v-table :entity="'users'" :is-multi-select="true" :rows="rows" :total="total">
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
        allSelected: false,
        selectApply: ''
      }
    },
    computed: {
      ...mapGetters({
        rows: 'table/rows',
        total: 'table/total'
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
        try {
          await axios.delete(`/ajax/admin/users/${data.username}`)
          this.$store.dispatch('session/setMessageSuccess', this.$tc('message.user_delete_ok',1))
          this.$store.dispatch('table/fetchData', {
            entity: 'users',
            queryString: this.$route.fullPath
          })
        } catch (e) {}
      },
      getSelectedRows (column = null) {
        let selected = []
        this.rows.forEach(row => {
          if (row.selected) {
            if (column) {
              selected.push(row[column])
            } else {
              selected.push(row)
            }
          }
        })
        return selected
      },
      async applyToSelected () {
        switch (this.selectApply) {
          case 'del':
            try {
              await axios.post(`/ajax/admin/users/batch/delete`,{users:this.getSelectedRows('username')})
              this.$store.dispatch('session/setMessageSuccess', this.$tc('message.user_delete_ok',2))
              this.$store.dispatch('table/fetchData', {
                entity: 'users',
                queryString: this.$route.fullPath
              })
            } catch (e) {

            }
            break
        }

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