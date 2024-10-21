<template>
  <li :key="name">
    <NuxtLink
      :href="href"
      :active-class="'hover:bg-primary/90 bg-primary text-white shadow'"
      class="group mx-2 flex h-9 items-center gap-x-4 rounded-md px-4 text-base font-semibold text-gray-800 duration-100 hover:bg-gray-200/60"
    >
      <component
        :is="icon"
        class="h-4 w-4 shrink-0"
        :active-class="'text-indigo-600'"
      />
      {{ name }}
    </NuxtLink>
  </li>
</template>

<script setup>
const props = defineProps(["name", "href", "icon"]);
</script>
