<template>
  <div
    class="flex h-[70px] cursor-pointer items-center justify-center rounded-md border bg-gray-100/20 px-10 text-base text-gray-700 duration-300"
    :class="
      selected
        ? 'border-gray-400 bg-white shadow-md'
        : 'border-gray-200 bg-white hover:border-gray-300'
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
