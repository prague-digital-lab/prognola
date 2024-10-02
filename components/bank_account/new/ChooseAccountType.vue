<template>
  <div
    class="flex h-[200px] items-center justify-center rounded-xl border border-gray-200 bg-gray-100/20 px-10 text-gray-700 duration-300"
    :class="
      selected
        ? 'border-transparent bg-white ring-1 ring-gray-200 ring-offset-2'
        : 'bg-white/20'
    "
  >
    <div class="text-center">
      <p class="mb-4">{{ name }}</p>

      <nuxt-link
        class="rounded-xl bg-black px-4 py-2 font-medium text-white"
        v-if="!selected"
        >Vybrat
      </nuxt-link>

      <nuxt-link
        class="rounded-xl bg-gray-500 px-4 py-2 font-medium text-white"
        v-else
        >Zvoleno
      </nuxt-link>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps(["selected", "name", "account_type"]);
</script>

<style scoped></style>
