<template>
  <NuxtLayout>
    <NuxtPage/>
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  title: 'Finance - Valašská pevnost',
  meta: [
    { name: 'description', content: 'Prognosol Finance.' }
  ],
  bodyAttrs: {
    class: 'test'
  },
})
</script>