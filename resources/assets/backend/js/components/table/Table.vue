<template>
    <div class="card col-lg p-0 m-0">
        <div class="table-container table-responsive">
            <template v-if="table.length===0">
                <h3>{{$t('tables.empty')}}</h3>
            </template>
            <template v-else>
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <slot name="header-select-all">
                            <th v-show="isMultiSelect">
                                <div class="form-check">
                                    <input class="form-check-input position-static"
                                           type="checkbox"
                                           :aria-label="$t('general.select_all')"
                                           :title="$t('general.select_all')"
                                           @click="toggleSelectAll">
                                </div>
                            </th>
                        </slot>
                        <th v-for="(info, index) in table.columns"
                            :key="index"
                            @click="sort(info)"
                            :style="{
                            'width': info.hasOwnProperty('width')?info.width:'auto'}">{{info.label}}<span
                                v-if="info.sortable"
                                :title="$t('tables.sort_'+getOrder(info.order))"><fa
                                class="float-right"
                                :icon="info.order===$t('filters.asc')?'angle-double-down':'angle-double-up'"></fa></span>
                        </th>
                        <slot name="header-action">
                            <th>
                                {{$t('general.actions')}}
                            </th>
                        </slot>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row,rowIdx) in table.rows"
                        :key="rowIdx">
                        <slot name="body-select-row" :row="row">
                            <td v-show="isMultiSelect">
                                <div class="form-check">
                                    <input class="form-check-input position-static"
                                           type="checkbox"
                                           :aria-label="$t('tables.select_item',{name:row[$t(`db_raw_inv.${selectColumnName}`)]})"
                                           :title="$t('tables.select_item',{name:row[$t(`db_raw_inv.${selectColumnName}`)]})"
                                           v-model="row.selected">
                                </div>
                            </td>
                        </slot>
                        <td v-for="(info,colIdx) in table.columns" :key="colIdx">
                            {{row[info.name]}}
                        </td>
                        <slot name="body-action" :row="row">
                        </slot>
                    </tr>
                    </tbody>
                </table>
                <div id="paginator_container" class="container mt-4">
                    <div class="row justify-content-md-center">
                        <div class="paginator col-lg-6">
                            <b-pagination-nav v-if="table.lastPage>1" :link-gen="linkGen" :total-rows="table.total"
                                              :value="table.currentPage"
                                              :per-page="table.perPage" :limit="10" :number-of-pages="table.lastPage">
                            </b-pagination-nav>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
  import Vue from 'vue'
  import axios from 'axios'
  import { PaginationNav } from 'bootstrap-vue/es/components'

  Vue.use(PaginationNav)

  export default {
    name: 'v-table',
    components: {
      PaginationNav
    },
    data: function () {
      return {
        sortOrder: 'desc',
        allSelected: false,
        columns:[],
        // table: {
        //   rows: [],
        //   currentPage: 1,
        //   from: 0,
        //   lastPage: 0,
        //   perPage: 0,
        //   to: 0,
        //   total: 0,
        // }
      }
    },
    props: {
      entity: {
        type: String,
        required: true
      },
      data: {
        type: Object,
        required: true
      },
      isMultiSelect: {
        type: Boolean,
        default: false
      },
      selectColumnName: {
        type: String,
        default: ''
      }
    },
    // watch: {
    //   data () {
    //     this.table = this.data
    //   },
    // },
    computed:{
      table(){
        return this.data
      }
    },
    methods: {
      toggleSelectAll () {
        this.allSelected = !this.allSelected
        this.table.rows.forEach(row => (row.selected = this.allSelected))
      },
      getSelectedRows (column = null) {
        let selected = []
        this.table.rows.forEach(row => {
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
      sort (column) {
        if (!column.sortable) {
          return
        }
        let obj = Object.assign({}, this.$route.query)
        obj[this.$t('filters.sortBy')] = this.$t(`db_raw.${column.name}`)
        let orderTranslated = this.$t('filters.order')
        obj[orderTranslated] = this.toggleSortOrder()
        this.updateColumn({columnName: column.name, direction: obj[orderTranslated]})
        this.$router.push({query: obj})
      },
      toggleSortOrder () {
        this.sortOrder = this.getOrder(this.sortOrder)
        return this.sortOrder
      },
      linkGen (pageNum) {
        let obj = {
          name: `admin.${this.entity}.index`, query: Object.assign({}, this.$route.query)
        }
        obj.query.page = pageNum
        return obj
      },
      updateColumn (obj) {
        this.$store.commit('table/UPDATE_TABLE_COLUMN', obj)
      },
      getOrder (val) {
        let asc = this.$t('filters.asc')
        let desc = this.$t('filters.desc')
        return val === asc ? desc : asc
      },
      getQueryString (string) {
        let qS = string.match(/\/[^?]+(.*)/)
        return (qS[1]) ? qS[1] : ''
      }
    },
  }
</script>