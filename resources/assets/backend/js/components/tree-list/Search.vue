<template>
    <div class="col-lg-4">
        <div class="input-group">
            <input type="text" class="form-control"
                   :placeholder="$t('pages.users.filter_full_name')"
                   :aria-label="$t('pages.users.filter_full_name')"
                   v-model="searchTerm"
                   @input="onInput($event.target.value)"
                   @keyup.esc="isOpen = false"
                   @blur="isOpen = false">
            <div class="input-group-append">
                <label class="input-group-text"
                       :title="$t('general.search')">
                    <fa icon="search"/>
                </label>
            </div>
        </div>
        <ul v-if="isOpen && filteredOptions.length > 0" class="options-list">
            <li v-for="(option,index) in filteredOptions"
                :key="index"
                @mousedown="search(option.path)">
                {{option.name}}
            </li>
        </ul>
    </div>
</template>

<script>
  export default {
    name: 'search',
    props: ['terms'],
    data () {
      return {
        searchTerm: '',
        isOpen: false
      }
    },
    methods: {
      search (path) {
        this.$emit('show', path)
      },
      onInput (value) {
        if (value.length > 0) {
          this.isOpen = !!value
        } else {
          this.isOpen = false
        }
      }
    },
    computed: {
      filteredOptions () {
        const re = new RegExp(this.searchTerm, 'i')
        return this.terms.filter(item => item.label.match(re))
      }
    }
  }
</script>