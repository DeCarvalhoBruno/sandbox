<template>
    <div class="table-container table-responsive">
        <template v-if="rows.length==0">
            <h3>{{$t('tables.empty')}}</h3>
        </template>
        <template v-else>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th v-for="(name,label,index ) in columns"
                        :key="index"
                        @click="sort(name)">
                        {{label}}<i v-if="sortableColumns.includes(name)" class="fa float-right" :class="['fa-sort-amount-'+'desc']"></i>
                    </th>
                    <th>
                        {{$t('general.actions')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(row,rowIdx) in rows"
                    :key="rowIdx">
                    <td v-for="(colName,cl,colIdx) in columns" :key="colIdx">
                        {{row[colName]}}
                    </td>
                    <td>
                        <div class="inline">
                            <router-link :to="{ name: 'admin.user.edit', params: { user_id: row.user_id } }">
                                <button class="btn btn-sm btn-info">
                                <span class="fa fa-pencil">
                            </span>
                                </button>
                            </router-link>
                            <button class="btn btn-sm btn-danger">
                                <span class="fa fa-trash-o">
                                <router-link :to="{ name: 'admin.dashboard' }">
                                </router-link>
                            </span>
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
        sortableColumns: 'table/sortableColumns',
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
          queryString: this.$route.fullPath
        })
      }
    },
    methods: {
      sort (column) {
        if (!this.sortableColumns.includes(column)) {
          return
        }

        let obj = Object.assign({}, this.$route.query)
        obj.sortByCol = column
        obj.order = this.toggleSortOrder()
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
      }
    }
  }
</script>