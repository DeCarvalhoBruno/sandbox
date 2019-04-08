<template>
  <div>
    <input :type="type"
           :name="name"
           :class="classes"
           :maxlength="maxlength"
           :autocomplete="autocomplete"
           @keyup="search"
           @blur="search"
           :required="required"
           v-model="currentValue">
    <div class="validator-valid" v-if="validated">
      <span class="fa-stack-1x icon">
        <i class="fa fa-circle fa-stack-1x" :class="[validationOk?'success':'danger']"></i>
        <i class="fa fa-stack-1x status" :class="[validationOk?'fa-check':'fa-times']"></i>
      </span>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: 'input-validator',
    props: {
      classes: {type: String},
      minlength: {type: Number, default: () => 0},
      maxlength: {type: Number},
      type: {type: String, default: () => 'text'},
      name: {type: String},
      value: {type: String},
      autocomplete: {type: String, default: () => 'off'},
      required: {type: Boolean, default: () => false},
      //scheme and host of the url used for ajax post search requests
      searchHostUrl: {type: String, required: true},
      searchField: {type: String, required: true},
      regexPattern: {type: String, default: () => '*'}
    },
    watch: {
      value () {
        this.currentValue = this.value
      }
    },
    computed: {
      regex () {
        return new RegExp(this.regexPattern, 'i')
      }
    },
    data () {
      return {
        currentValue: null,
        validated: false,
        searchTriggerLength: 5,
        lastInput: null,
        searchTriggerDelay: 2000,
        timer: 0,
        validationOk: false
      }
    },
    methods: {
      async search (e) {
        let inputValue = e.target.value

        if (
          inputValue.length > this.searchTriggerLength
          && inputValue !== this.lastInput
          && inputValue.length >= this.minLength
          && inputValue.length <= this.maxLength
          && inputValue.match(this.regex)
        ) {
          this.lastInput = inputValue
          clearTimeout(this.timer)
          let vm = this
          this.timer = setTimeout(async function () {
            const {data} = await axios.post(`${vm.searchHostUrl}/search/check`,
              {q: e.target.value, field: vm.searchField})
            vm.validated = true
            if (data.hasOwnProperty('cnt')) {
              vm.validationOk = data.cnt === 0
            }
          }, this.searchTriggerDelay)
        }
      }
    }
  }
</script>