<template>
  <div class="base--layout">
    <router-view :key="$route.path"></router-view>
  </div>
</template>

<script>
export default {
name: "Base"
}
</script>

<style scoped>

</style>