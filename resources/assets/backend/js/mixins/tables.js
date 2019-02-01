import axios from 'axios'

export default {
  name: 'table-mixin',
  data: function () {
    return {}
  },
  computed: {
    computedTable () {
      return this.data
    }
  },
  watch: {
    '$route' () {
      this.setFilterButtons()
      this.setIntendedRoute()
      this.refreshTableData()
    }
  },
  created () {
    this.setFilterButtons()
    this.setIntendedRoute()
  },
  methods: {
    refreshTableData () {
      let vm = this
      axios.get(`/ajax${this.$route.fullPath}`).then(({data}) => {
        vm.getInfo(data, true)
      })
    },
    setFilterButtons () {
    },
    setIntendedRoute () {
      return this.$store.dispatch('session/setIntendedUrl',
        {url: this.$router.currentRoute})
    },
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
    },
    setFilterButton (type) {
      let filterTranslation = this.$t(`filters.${this.entity}_${type}`)
      this[`${type}Filter`] = this.$route.query[filterTranslation] || ''
      if (this[`${type}Filter`] !== '') {
        this.$set(this.filterButtons, filterTranslation, this[`${type}Filter`])
      }
    },
    getInfo (data, refresh) {
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
        sorted: data.sorted
      }
    }
  },
  beforeRouteEnter (to, from, next) {
    axios.get(`/ajax${to.fullPath}`).then(({data}) => {
      next(vm => vm.getInfo(data, false))
    })
  }
}
