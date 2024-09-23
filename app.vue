<template>
  <NuxtLayout>
    <NuxtPage :keepalive="false"/>
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  title: 'Prognola',
  meta: [
    { name: 'description', content: 'Prognola Finance.' }
  ],
  bodyAttrs: {
    class: 'test'
  },
})
</script>