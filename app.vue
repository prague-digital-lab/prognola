<template>
  <NuxtLayout>
    <NuxtPage :keepalive="false"/>
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  title: 'Prognola - Veďte firmu jako nikdy předtím',
  meta: [
    { name: 'description', content: 'Prognola Finance.' }
  ],
  bodyAttrs: {
  },
})
</script>