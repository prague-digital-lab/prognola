<template>
  <NuxtLayout>
    <NuxtPage :keepalive="false" />
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  meta: [{ name: "description", content: "Prognola" }],
  bodyAttrs: {},
});
</script>
