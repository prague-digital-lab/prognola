<template>
  <NuxtLayout>
    <NuxtPage :keepalive="false" />
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  title: "Prognola - Budoucnost řízení firemních financí je tady.",
  meta: [{ name: "description", content: "Prognola" }],
  bodyAttrs: {},
});
</script>
