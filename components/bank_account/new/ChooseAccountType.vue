<template>
  <div
    class="flex h-[200px] items-center justify-center rounded-md border bg-gray-100/20 px-10 text-gray-700 duration-300"
    :class="
      selected
        ? 'border-gray-400 bg-white shadow-md'
        : 'border-gray-200 bg-white hover:border-gray-300'
    "
  >
    <div class="text-center">
      <p class="mb-4">{{ name }}</p>

      <nuxt-link
        class="cursor-pointer rounded-full bg-black px-4 py-2 text-base font-medium text-white transition duration-200 hover:bg-gray-700"
        v-if="!selected"
        >Vybrat
      </nuxt-link>

      <nuxt-link
        class="rounded-full border border-gray-200 bg-gray-50/50 px-4 py-2 text-base text-gray-400"
        v-else
        >Vybráno
      </nuxt-link>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps(["selected", "name", "account_type"]);
</script>

<style scoped></style>
