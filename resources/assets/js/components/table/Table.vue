<template>
    <div class="table-container table-responsive">
        <template v-if="rows.length==0">
            <h3>{{$t('tables.empty')}}</h3>
        </template>
        <template v-else>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th v-for="(info,index) in columns"
                        :key="index"
                        @click="sort(info)">
                        {{info.label}}<span v-if="info.sortable" :title="$t('tables.sort_'+(info.order=='asc'?'desc':'asc'))">
                        <fa class="float-right"
                            :icon="info.order=='asc'?'angle-double-down':'angle-double-up'"/></span>
                    </th>
                    <th>
                        {{$t('general.actions')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(row,rowIdx) in rows"
                    :key="rowIdx">
                    <td v-for="(info,colIdx) in columns" :key="colIdx">
                        {{row[info.name]}}
                    </td>
                    <td>
                        <div class="inline">
                            <router-link :to="{ name: 'admin.user.edit', params: { user: row.username } }">
                                <button class="btn btn-sm btn-info">
                                    <fa icon="pencil-alt">
                                    </fa>
                                </button>
                            </router-link>
                            <button class="btn btn-sm btn-danger">
                                <fa icon="trash-alt">
                                    <router-link :to="{ name: 'admin.dashboard' }">
                                    </router-link>
                                </fa>
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="paginator">
                <b-pagination-nav :link-gen="linkGen" :total-rows="total" :value="currentPage"
                                  :per-page="perPage" :limit="perPage" :number-of-pages="lastPage">
                </b-pagination-nav>
            </div>
        </template>
    </div>
</template>

<script>
  import Vue from 'vue'

  import { mapGetters } from 'vuex'
  import { PaginationNav } from 'bootstrap-vue/es/components'

  Vue.use(PaginationNav)

  export default {
    name: 'v-table',
    components: {
      PaginationNav
    },
    data: function () {
      return {
        sortOrder: 'desc'
      }
    },
    props: {
      entity: {
        type: String,
        required: true
      }
    },
    computed: {
      ...mapGetters({
        rows: 'table/rows',
        columns: 'table/columns',
        currentPage: 'table/currentPage',
        from: 'table/from',
        to: 'table/to',
        lastPage: 'table/lastPage',
        perPage: 'table/perPage',
        total: 'table/total'
      })
    },
    watch: {
      '$route' () {
        this.$store.dispatch('table/fetchData', {
          entity: 'users',
          queryString: this.$route.fullPath,
          refresh: true
        })
      }
    },
    methods: {
      sort (column) {
        if (!column.sortable) {
          return
        }
        let obj = Object.assign({}, this.$route.query)
        obj.sortByCol = column.name
        obj.order = this.toggleSortOrder()
        this.updateColumn({columnName: column.name, direction: obj.order})
        this.$router.push({query: obj})
      },
      toggleSortOrder () {
        this.sortOrder = (this.sortOrder == 'asc') ? 'desc' : 'asc'
        return this.sortOrder
      },
      linkGen (pageNum) {
        let obj = {
          name: 'admin.user.index', query: Object.assign({}, this.$route.query)
        }
        obj.query.page = pageNum
        return obj
      },
      updateColumn (obj) {
        this.$store.commit('table/UPDATE_TABLE_COLUMN', obj)
      }
    }
  }
</script>