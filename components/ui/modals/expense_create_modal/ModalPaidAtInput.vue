<template>
  <div class="inline-block">
    <input
      class="me-2 cursor-default rounded border border-gray-200 px-2 py-1 text-sm text-gray-800 shadow-sm hover:bg-gray-100 focus-visible:outline-0 active:bg-gray-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
      type="date"
      autofocus
      v-model="paid_at"
    />
  </div>
</template>

<script setup>
import { DateTime } from "luxon";

const paid_at = defineModel();

onMounted(() => {
  if (paid_at.value === undefined) {
    paid_at.value = DateTime.now().startOf("day").toFormat("yyyy-MM-dd");
  }
});

const paid_at_luxon = computed(() => {
  return DateTime.fromFormat(paid_at.value, "yyyy-MM-dd");
});
</script>

<style scoped></style>
