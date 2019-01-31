export default {
  name: 'table-mixin',
  watch: {
    '$route' () {
      this.setFilterButtons()
    }
  },
  data: function () {
    return {}
  },
  methods: {
    removeFilter (obj) {
      this.filterButtons = obj
    },
    resetFilters () {
      this.filterButtons = {}
    },
    applyFilter (type) {
      let filter = this[`${type}Filter`]
      if (filter) {
        let obj = Object.assign({}, this.$route.query)
        obj[this.$t(`filters.${this.entity}_${type}`)] = filter
        this.$router.push({query: obj})
      }
    },
    applyMethod (name) {
      this[name]()
      // this.$store.commit('session/HIDE_MODAL')
    },
    setFilterButton (type) {
      let filterTranslation = this.$t(`filters.${this.entity}_${type}`)
      this[`${type}Filter`] = this.$route.query[filterTranslation] || ''
      if (this[`${type}Filter`] !== '') {
        this.$set(this.filterButtons, filterTranslation, this[`${type}Filter`])
      }
    },
    getInfo (data, refresh) {
      // if (refresh === true) {
      //   this.data = {
      //     rows: data.table.data,
      //     currentPage: data.table.current_page,
      //     from: data.table.from,
      //     lastPage: data.table.last_page,
      //     perPage: data.table.per_page,
      //     to: data.table.to,
      //     total: data.table.total
      //   }
      //   // commit(types.UPDATE_TABLE_DATA, data)
      // } else {
      this.data = {
        rows: data.table.data,
        columns: data.columns,
        groups: data.groups,
        currentPage: data.table.current_page,
        from: data.table.from,
        lastPage: data.table.last_page,
        perPage: data.table.per_page,
        to: data.table.to,
        total: data.table.total,
        extras: {groups: data.groups}
      }
      // }
    }
  }
}
