<template>
  <card :title="$t(general.home')">

  </card>
</template>

<script>
export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('general.home') }
  }
}
</script>
