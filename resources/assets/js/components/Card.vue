<template>
  <div class="card">
    <div class="card-header">
      {{  }}
    </div>

    <div class="card-body">
      <slot/>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Card',

  props: ['title']
}
</script>
