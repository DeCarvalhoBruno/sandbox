<template>
    <div>
        I'm a user!
    </div>
</template>

<script>
  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'users',
    data () {
      return {}
    }
  }
</script>