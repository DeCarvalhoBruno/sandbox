<template>
  <div class="btn-group btn-group-toggle">
    <label v-for="(choice, idx) in choices" @click="checked(idx)" class="btn"
           :class="activeItem===idx?'active btn-info':'btn-light'">
      <input type="radio" name="fieldName" autocomplete="off" :checked="activeItem===idx"> {{choice}}
    </label>
  </div>
</template>

<script>
  export default {
    name: 'button-group',
    props: {
      active: null,
      fieldName: {required: true, type: String},
      choices: {}
    },
    data () {
      return {
        activeItem: null
      }
    },
    mounted () {
      this.activeItem = this.active
    },
    methods: {
      checked (checked) {
        this.activeItem = checked
        this.$emit('active-changed', checked)
      }
    }
  }
</script>