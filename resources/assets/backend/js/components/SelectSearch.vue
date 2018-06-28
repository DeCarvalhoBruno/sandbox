<template>
    <div class="container m-0 p-0">
        <div class="row">
            <div class="search-spinner-wrapper">
                <fa icon="cog" size="lg" :spin="loadIconIsAnimated"/>
            </div>
            <select v-model="selectedItem">
                <option v-for="(result, index) in searchResults"
                        :key="index"
                        :value="result.text">{{index}}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'select-search',
    props:{
      searchUrl: String,
      limit: {
        default: 5
      }
    },
    data () {
      return {
        input: '',
        oldInput: '',
        hiddenInput: '',
        searchResults: [],
        searchSelection: 0,
        searchTriggerDelay: 300,
        searchTriggerLength: 1,
        lastSearchTime: 0,
        loadIconIsAnimated: false,
        selectedItem: null
      }
    },
    methods: {
      async searchTag (e, value) {
        let input = (typeof value === 'undefined') ? this.input.trim() : value
        if (this.typeahead === true) {
          let now = Date.now ? Date.now() : new Date().getTime()
          if ((input.length < this.searchTriggerLength) || (this.lastSearchTime > now - this.searchTriggerDelay)) {
            return
          }
          this.lastSearchTime = now
          if (this.oldInput !== input) {
            this.loadIconIsAnimated = true
            this.searchResults = await this.searchTerm(input)
            this.loadIconIsAnimated = false
            this.searchSelection = 0

            if (input.length) {
              for (let id in this.existingTags) {
                let text = this.existingTags[id].toLowerCase()

                if (text.search(input.toLowerCase()) > -1) {
                  this.searchResults.push({id, text: this.existingTags[id]})
                }
              }
            }

            this.oldInput = input
          }
        }
      }
    }
  }
</script>