<template>
  <div class="card">
    <h3 class="mb-4">{{ $t('error.page_not_found') }}</h3>

    <div class="links">
      <router-link :to="{ name: 'admin.dashboard' }">
        {{ $t('go_home') }}
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound'
}
</script>
