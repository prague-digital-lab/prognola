<template>
  <NuxtLayout>
    <NuxtPage :keepalive="false" />
  </NuxtLayout>
</template>

<script setup lang="ts">
useHead({
  title: "Prognola - Plánujte firemní finance s lehkostí",
  meta: [{ name: "description", content: "Prognola" }],
  bodyAttrs: {},
});
</script>
