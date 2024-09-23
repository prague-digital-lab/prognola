<template>
  <NuxtLayout>
    <NuxtPage :keepalive="false"/>
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  title: 'Prognola',
  meta: [
    { name: 'description', content: 'Prognosol Finance.' }
  ],
  bodyAttrs: {
    class: 'test'
  },
})
</script>