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
                        @click="sort(name)"
                    >
                        {{label}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(row,rowIdx) in rows"
                    :key="rowIdx"
                >
                    <td v-for="(colName,cl,colIdx) in columns" :key="colIdx"
                    >
                        {{row[colName]}}
                    </td>

                </tr>
                </tbody>
            </table>
        </template>
    </div>
</template>

<script>
  export default {
    name: 'v-table',
    data: function () {
      return {
        sortOrder: 'desc'
      }
    },
    props: {
      rows: null,
      columns: null,
      sortableColumns: null,
      entity: null
    },
    mounted () {

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
        this.$router.push({query: {sortByCol: column, order: this.toggleSortOrder()}})
      },

      toggleSortOrder () {
        this.sortOrder = (this.sortOrder=='asc') ? 'desc' : 'asc'
        return this.sortOrder
      }

    }
  }
</script>