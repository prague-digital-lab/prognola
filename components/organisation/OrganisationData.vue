<template>
  <div
    class="mb-4 rounded-md border border-zinc-200 p-5 dark:border-zinc-800 dark:text-zinc-300"
  >
    <p>IČ: {{ organisation.ic }}</p>
    <p>DIČ: {{ organisation.dic }}</p>
    <p>Ulice: {{ organisation.street }}</p>
    <p>Město: {{ organisation.city }}</p>
    <p>PSČ: {{ organisation.postal }}</p>
    <p>Země: {{ organisation.country }}</p>
  </div>
</template>

<script setup lang="ts">
const props = defineProps(["organisation"]);
</script>

<style scoped></style>