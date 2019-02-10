<template>
    <button :type="nativeType" :disabled="isLoading" class="btn" :class="{
    [`btn-${type}`]: true,
    'btn-block': block,
    'btn-lg': large,
    'btn-loading': isLoading
  }">
        <slot></slot>
    </button>
</template>

<script>
  export default {
    name: 'submit-button',
    props: {
      type: {
        type: String,
        default: 'primary'
      },
      nativeType: {
        type: String,
        default: 'submit'
      },
      loading: {
        type: Boolean,
        default: false
      },
      block: {
        type: Boolean,
        default: false
      },
      large: {
        type: Boolean,
        default: false
      }
    },
    data () {
      return {
        isLoading: false
      }
    },
    watch: {
      loading () {
        this.isLoading = this.loading
      }
    },
    methods: {
      toggleLoad (value) {
        this.isLoading = value
      }
    }
  }
</script>
