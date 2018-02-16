<template>
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">title</h4>
        <p class="card-text">text</p>
        <a href="#" class="btn btn-primary">button</a>
      </div>
    </div>


</template>

<script>
  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'user-edit',
    data () {
      return {}
    }
  }
</script>