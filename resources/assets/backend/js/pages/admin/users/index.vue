<template>
    <div class="container">
        <div class="row">
            <div class="container">
                <table-filter :filterButtons="filterButtons" @table-filter-removed="removeFilter"/>
                <div class="row pb-1">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :placeholder="$t('pages.users.filter_full_name')"
                                   :aria-label="$t('pages.users.filter_full_name')"
                                   v-model="nameFilter"
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
                            <select class="custom-select" id="inputGroupSelect02" v-model="createdFilter">
                                <option disabled value="">{{$t('pages.users.filter_created_at')}}</option>
                                <option value="day">Registered today</option>
                                <option value="week">Registered less than a week ago</option>
                                <option value="month">Registered less than a month ago</option>
                                <option value="year">Registered less than a year ago</option>
                            </select>
                            <div class="input-group-append">
                                <label class="input-group-text"
                                       :title="$t('general.search')">
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
            <v-table ref="table" :entity="'users'" :is-multi-select="true" :rows="rows"
                     :total="total" select-column-name="full_name">
                <td slot="body-action" slot-scope="props">
                    <div class="inline">
                        <template v-if="props.row.username">
                            <router-link :to="{ name: 'admin.users.edit', params: { user: props.row.username } }">
                                <button type="button" class="btn btn-sm btn-info"
                                        :title="$t('tables.edit_item',{name:props.row[$t('db_raw_inv.full_name')]})">
                                    <fa icon="pencil-alt">
                                    </fa>
                                </button>
                            </router-link>
                        </template>
                        <button type="button" class="btn btn-sm btn-danger"
                                :title="$t('tables.delete_item',{name:props.row[$t('db_raw_inv.full_name')]})"
                                @click="deleteRowConfirm(props.row)">
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
  import TableFilter from '~/components/table/TableFilter'
  import { mapGetters } from 'vuex'
  import axios from 'axios'

  Vue.use(Table)

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'users',
    components: {
      'v-table': Table,
      TableFilter
    },
    data: function () {
      return {
        allSelected: false,
        selectApply: '',
        groupFilter: null,
        createdFilter: null,
        nameFilter: null,
        filterButtons: {},
        selectionBuffer: {}
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
        this.applyFilter('group')
      },
      createdFilter () {
        this.applyFilter('created')
      },
      '$route' () {
        this.setFilterButtons()
      }
    },
    created () {
      this.setFilterButtons()
      this.$root.$on('modal_confirmed', this.applyMethod)
    },
    methods: {
      removeFilter (obj) {
        this.filterButtons = obj
      },
      applyFilter (type) {
        let filter = this[`${type}Filter`]
        if (filter) {
          let obj = Object.assign({}, this.$route.query)
          obj[this.$t(`filters.${type}`)] = filter
          this.$router.push({query: obj})
        }
      },
      applyMethod (name) {
        this[name]()
        this.$store.commit('session/HIDE_MODAL')
      },
      setFilterButtons () {
        this.setFilterButton('name')
        this.setFilterButton('group')

        this.createdFilter = this.$route.query[this.$t('filters.created')] || ''
        if (this.createdFilter !== '') {
          this.$set(this.filterButtons, this.$t('filters.created'), this.$t(`filters.${this.createdFilter}`))
        }
      },
      setFilterButton (type) {
        let filterTranslation = this.$t(`filters.${type}`)
        this[`${type}Filter`] = this.$route.query[filterTranslation] || ''
        if (this[`${type}Filter`] !== '') {
          this.$set(this.filterButtons, filterTranslation, this[`${type}Filter`])
        }
      },
      deleteRowConfirm (data) {
        this.$store.dispatch('session/showModal', {
          data: {
            method: 'deleteRow',
            title: this.$t('modal.user_delete.h'),
            confirmBtnClass: 'btn-danger',
            confirmBtnText: this.$t('general.delete'),
            text: '<h3>' + this.$t('modal.user_delete.t', {name: data.full_name}) + '</h3>'
          }
        })
        this.selectionBuffer = data
      },
      async deleteRow () {
        try {
          await axios.delete(`/ajax/admin/users/${this.selectionBuffer.username}`)
          this.$store.dispatch(
            'session/setAlertMessageSuccess',
            this.$tc('message.user_delete_ok', 1, {name: this.selectionBuffer.full_name})
          )
          this.$store.dispatch('table/fetchData', {
            entity: 'users',
            queryString: this.$route.fullPath
          })
        } catch (e) {}
      },
      async applyToSelected () {
        switch (this.selectApply) {
          case 'del':
            try {
              await axios.post(`/ajax/admin/users/batch/delete`, {users: this.$refs.table.getSelectedRows('username')})
              this.$store.dispatch('session/setAlertMessageSuccess', this.$tc('message.user_delete_ok', 2))
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
        obj[this.$t('filters.name')] = this.nameFilter
        this.$router.push({query: obj})
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