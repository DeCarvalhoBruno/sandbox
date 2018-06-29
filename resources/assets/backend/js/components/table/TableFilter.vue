<template>
    <div class="row mb-5">
        <div class="col-md-2">
            <button type="button" class="btn btn-primary" @click="resetFilters">
                {{$t('general.reset_filters')}}
            </button>
        </div>
        <div id="filters_list" class="col-md-4">
            <span
                    class="btn btn-default btn-outline-warning ml-2"
                    v-for="(button,idx) in filters"
                    :key="idx"
                    v-model="filters"
                    @click="removeFilter(idx)"
            >{{button}}<button type="button"
                               class="close button-list-close"
                               aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
            </span>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'table-filter',
    props: {
      filterButtons: Object
    },
    computed: {
      filters () {
        return this.filterButtons
      }
    },
    data () {
      return {}
    },
    methods: {
      resetFilters () {
        this.filters = {}
        this.$router.push({query: null})
      },
      removeFilter (idx) {
        let currentFilters = Object.assign({}, this.$route.query)
        delete currentFilters[idx]
        let obj = Object.assign({}, this.filters)
        delete obj[idx]
        this.$emit('table-filter-removed', obj)
        this.$router.push({query: currentFilters})
      }
    }
  }
</script>