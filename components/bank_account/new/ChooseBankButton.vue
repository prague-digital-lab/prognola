<template>
  <div
    class="flex h-[70px] items-center justify-center rounded-xl border border-gray-200 px-10 text-gray-700 text-sm duration-200"
    :class="
      selected
        ? 'border-transparent bg-white ring-1 ring-gray-200 ring-offset-2'
        : 'bg-white/20'
    "
  >
    <div class="text-center">
      <p class="">{{ name }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps(["selected", "name"]);
</script>

<style scoped></style>
