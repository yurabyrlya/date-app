<template>
<div>
    <div v-if="$route.path === '/'">
      <login/>
    </div>
    <div v-if="$route.path === '/profile'">
      <profile/>
    </div>

</div>
</template>

<script>
export default {
  name: "App"
}
</script>

<style scoped>

</style>