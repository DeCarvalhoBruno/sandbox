<template>
    <div class="container">
        <div class="row">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" @click="resetFilters">
                            {{$t('general.reset_filters')}}
                        </button>
                    </div>
                    <div id="filters_list" class="col-md-4">
                        <span
                                class="btn btn-default btn-outline-warning ml-2"
                                v-for="(button,idx) in filterButtons"
                                :key="idx" v-model="filterButtons"
                        >{{button}}<button type="button" class="close button-list-close"
                                           aria-label="Close"
                                           @click="removeFilter(idx)">
                                <span aria-hidden="true">&times;</span>
                                        </button>
                        </span>
                    </div>
                </div>
                <div class="row pb-1">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :placeholder="$t('pages.users.filter_full_name')"
                                   :aria-label="$t('pages.users.filter_full_name')"
                                   v-model="fullNameFilter"
                                   @keyup.enter="filterFullName">
                            <div class="input-group-append">
                                <button class="btn btn-default btn-outline-dark"
                                        type="button" :title="$t('general.search')"
                                        @click="filterFullName">
                                    <fa icon="user"/>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <select class="custom-select" v-model="groupFilter">
                                <option disabled value="">{{$t('pages.users.filter_group')}}</option>
                                <option v-for="(group,idx) in extras.groups" :key="idx">{{group}}</option>
                            </select>
                            <div class="input-group-append">
                                <label class="input-group-text"
                                       :title="$t('general.search')">
                                    <fa icon="users"/>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <select class="custom-select" id="inputGroupSelect02" v-model="createdAtFilter">
                                <option disabled value="">{{$t('pages.users.filter_created_at')}}</option>
                                <option value="day">Registered today</option>
                                <option value="week">Registered less than a week ago</option>
                                <option value="month">Registered less than a month ago</option>
                                <option value="year">Registered less than a year ago</option>
                            </select>
                            <div class="input-group-append">
                                <label class="input-group-text"
                                       type="button" :title="$t('general.search')"
                                       @click="filterCreatedAt">
                                    <fa icon="calendar"/>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row pb-1">
                    <div class="col-md-8">
                        <div class="form-row align-items-center">
                            <div class="col my-1">
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
        selectApply: '',
        groupFilter: '',
        createdAtFilter: '',
        fullNameFilter: '',
        filterButtons: {}
      }
    },
    computed: {
      ...mapGetters({
        rows: 'table/rows',
        total: 'table/total',
        extras: 'table/extras'
      })
    },
    watch: {
      groupFilter () {
        if (this.groupFilter) {
          let obj = Object.assign({}, this.$route.query)
          obj[this.$t('filters.group')] = this.groupFilter
          this.$router.push({query: obj})
        }
      },
      createdAtFilter () {
        if (this.createdAtFilter) {
          let obj = Object.assign({}, this.$route.query)
          obj[this.$t('filters.created')] = this.createdAtFilter
          this.$router.push({query: obj})
        }
      },
      '$route' () {
        this.setFilterButtons()

      }
    },
    created () {
      this.setFilterButtons()
    },
    methods: {
      setFilterButtons () {
        this.fullNameFilter = this.$route.query[this.$t('filters.name')]
        if (this.fullNameFilter) {
          this.$set(this.filterButtons, this.$t('filters.name'), this.fullNameFilter)
        }
        this.groupFilter = this.$route.query[this.$t('filters.group')]
        if (this.groupFilter) {
          this.$set(this.filterButtons, this.$t('filters.group'), this.groupFilter)
        } else {
          this.groupFilter = ''
        }
        this.createdAtFilter = this.$route.query[this.$t('filters.created')]
        if (this.createdAtFilter) {
          this.$set(this.filterButtons, this.$t('filters.created'), this.$t('filters.'+this.createdAtFilter))
        } else {
          this.createdAtFilter = ''
        }
      },
      resetFilters () {
        this.filterButtons = {}
        this.$router.push({query: null})
      },
      removeFilter (idx) {
        let currentFilters = Object.assign({}, this.$route.query)
        delete currentFilters[idx]
        let obj = Object.assign({}, this.filterButtons)
        delete obj[idx]
        this.filterButtons = obj
        this.$router.push({query: currentFilters})
      },
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
        // @TODO: code this
      },
      async deleteRow (data) {
        try {
          await axios.delete(`/ajax/admin/users/${data.username}`)
          this.$store.dispatch('session/setMessageSuccess', this.$tc('message.user_delete_ok', 1))
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
              await axios.post(`/ajax/admin/users/batch/delete`, {users: this.getSelectedRows('username')})
              this.$store.dispatch('session/setMessageSuccess', this.$tc('message.user_delete_ok', 2))
              this.$store.dispatch('table/fetchData', {
                entity: 'users',
                queryString: this.$route.fullPath
              })
            } catch (e) {}
            break
        }
      },
      filterFullName () {
        let obj = Object.assign({}, this.$route.query)
        obj[this.$t('filters.name')] = this.fullNameFilter
        this.$router.push({query: obj})
      },
      filterCreatedAt () {

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