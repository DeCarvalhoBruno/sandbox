<template>
    <div>
        <div class="form-control input-tag">
            <span class="badge badge-pill badge-light"
                  v-for="(badge, index) in tagBadges"
                  :key="index">
                <span v-html="badge"></span>
                <i href="#" class="button-badge-close" @click.prevent="removeTag(index)"></i>
            </span>
            <input type="text"
                   :placeholder="dataPlaceholder"
                   v-model="input"
                   ref="search"
                   @focus="dataPlaceholder=''"
                   @blur="dataPlaceholder=placeholder"
                   @keypress.enter.prevent="tagFromInput"
                   @keypress.backspace="removeLastTag"
                   @keypress.down="nextSearchResult"
                   @keypress.up="prevSearchResult"
                   @keypress.esc="ignoreSearchResults"
                   @keyup="searchTag"
                   @value="tags">
            <input type="hidden" :name="elementId" :id="elementId" v-model="hiddenInput">
        </div>
        <p v-show="searchResults.length" class="search-results-area">
            <span v-for="(tag, index) in searchResults"
                  :key="index"
                  v-text="tag.text"
                  @click="tagFromSearch(tag)"
                  class="badge"
                  v-bind:class="{
                    'badge-primary': index == searchSelection,
                    'badge-dark': index != searchSelection
                }"></span>
        </p>
    </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: 'input-tag',
    props: {
      elementId: String,
      existingTags: {
        type: [Array, Object],
        default: () => {
          return []
        }
      },
      oldTags: [Array, String],
      typeahead: Boolean,
      placeholder: String,
      searchUrl:String,
    },

    data () {
      return {
        badgeId: 0,
        tagBadges: [],
        tags: [],
        tagBag: [],

        input: '',
        oldInput: '',
        hiddenInput: '',

        searchResults: [],
        searchSelection: 0,
        dataPlaceholder: this.placeholder,
        searchTriggerDelay: 800,
        searchTriggerLength: 2,
        lastSearchTime: 0
      }
    },

    created () {
      if (this.oldTags && this.oldTags.length) {
        let oldTags = Array.isArray(this.oldTags)
          ? this.oldTags
          : this.oldTags.split(',')

        for (let id of oldTags) {
          let existingTag = this.existingTags[id]
          let text = existingTag ? existingTag : id

          this.addTag(id, text)
        }
      }
    },

    watch: {
      tags () {
        // Updating the hidden input
        this.hiddenInput = this.tags.join(',')
        this.$emit('updateAddedItems',this.tagBag)
      }
    },

    methods: {
      tagFromInput (e) {
        // If we're choosing a tag from the search results
        if (this.searchResults.length && this.searchSelection >= 0) {
          this.tagFromSearch(this.searchResults[this.searchSelection])

          this.input = ''
        } else {
          let text = this.input.trim()

          // If the new tag is not an empty string
          if (text.length) {
            this.input = ''

            // Determine the tag's id and text depending on if the tag exists
            // on the site already or not
            let id = this.makeSlug(text)
            let existingTag = this.existingTags[id]

            id = existingTag ? id : text
            text = existingTag ? existingTag : text

            this.addTag(id, text)
          }
        }
      },

      tagFromSearch (tag) {
        this.searchResults = []
        this.input = ''
        this.$refs.search.focus()
        this.addTag(tag.id, tag.text)
      },

      makeSlug (value) {
        return value.toLowerCase().replace(/\s/g, '-')
      },

      addTag (id, text) {
        // Attach the tag if it hasn't been attached yet
        let searchSlug = this.makeSlug(id)
        let found = this.tags.find((text) => {
          return searchSlug == this.makeSlug(text)
        })

        if (!found) {
          this.tagBadges.push(text.replace(/\s/g, '&nbsp;'))
          this.tags.push(id)
          this.tagBag.push({id,text})
        }
      },

      removeLastTag (e) {
        if (!e.target.value.length) {
          this.removeTag(this.tags.length - 1)
        }
      },

      removeTag (index) {
        this.tags.splice(index, 1)
        this.tagBadges.splice(index, 1)
        this.tagBag.splice(index, 1)
        this.$refs.search.focus()
      },

      async searchTag () {
        if (this.typeahead === true) {
          let now = Date.now ? Date.now() : new Date().getTime()
          if ((this.input.length < this.searchTriggerLength) || (this.lastSearchTime > now - this.searchTriggerDelay)) {
            return
          }

          this.lastSearchTime = now
          if (this.oldInput !== this.input) {
            this.$emit('searching')
            this.searchResults = await this.searchTerm(this.input)
            this.$emit('searched')
            this.searchSelection = 0
            let input = this.input.trim()

            if (input.length) {
              for (let id in this.existingTags) {
                let text = this.existingTags[id].toLowerCase()

                if (text.search(input.toLowerCase()) > -1) {
                  this.searchResults.push({id, text: this.existingTags[id]})
                }
              }
            }

            this.oldInput = this.input
          }
        }
      },

      async searchTerm (term) {
        let {data} = await axios.get(this.searchUrl+term)
        return data
      },

      nextSearchResult () {
        if (this.searchSelection + 1 <= this.searchResults.length - 1) {
          this.searchSelection++
        }
      },

      prevSearchResult () {
        if (this.searchSelection > 0) {
          this.searchSelection--
        }
      },

      ignoreSearchResults () {
        this.searchResults = []
        this.searchSelection = 0
      }
    }
  }
</script>