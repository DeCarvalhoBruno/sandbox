<template>
  <div>
    <div class="input-group input-group-search">
      <input type="text" id="navbar-search-input" minlength="1"
             class="form-control border-0 bg-light input-search"
             placeholder="Search..." ref="searchInput"
             @keyup="search" @keyup.enter.prevent="goToSearchPage" required>
      <button class="btn btn-light" type="button" @click="toggleFocus" ref="searchControl"><i class="fa fa-search"></i>
      </button>
    </div>
    <div id="search-result-container">
      <slot name="search-results" :row="searchData"></slot>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: 'inline-search',
    props: {
      searchPage: {required: true}
    },
    data () {
      return {
        lastInput: null,
        searchTriggerDelay: 300,
        searchTriggerLength: 2,
        timer: 0,
        searchData: null
      }
    },
    methods: {
      goToSearchPage () {
        // document.location.href = this.searchPage
      },
      async search (e) {
        let inputValue = e.target.value
        if (inputValue.length > this.searchTriggerLength && inputValue !== this.lastInput) {
          clearTimeout(this.timer)
          let vm = this
          this.timer = setTimeout(async function () {
            const {data} = await axios.post('/search', {q: e.target.value})
            if (data.status == 'ok') {
              vm.searchData = data
            }
          }, this.searchTriggerDelay)
        }

      },
      toggleFocus (e) {
        let si = this.$refs.searchInput
        if (si.clientWidth === 0) {
          si.focus()
        }
      }
    }
  }
</script>