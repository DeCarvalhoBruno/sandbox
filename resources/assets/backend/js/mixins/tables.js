import { mapGetters } from 'vuex'

export default {
  name: 'table-mixin',
  watch: {
    '$route' () {
      this.setFilterButtons()
    }
  },
  computed: {
    ...mapGetters({
      rows: 'table/rows',
      total: 'table/total',
      extras: 'table/extras'
    })
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
      this.$store.commit('session/HIDE_MODAL')
    },
    setFilterButton (type) {
      let filterTranslation = this.$t(`filters.${this.entity}_${type}`)
      this[`${type}Filter`] = this.$route.query[filterTranslation] || ''
      if (this[`${type}Filter`] !== '') {
        this.$set(this.filterButtons, filterTranslation, this[`${type}Filter`])
      }
    },
  }
}
