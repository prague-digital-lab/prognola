<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "index.vue",
});
</script>

<script setup lang="ts">
definePageMeta({
  layout: "guest",
});
</script>

<template>
  <div>
    <h1 class="mb-[40px] text-[45px] font-medium text-white">
      Plánujte své finance jako profesionál.
    </h1>

    <p class="mb-[60px] text-[20px] font-medium text-gray-400">
      Získejte zpět přehled o aktuálním stavu financí ve vaší firmě.
      <br />Konečně budete mít prognózu, kolik budete mít peněz v nadcházejících
      obdobích.
    </p>

    <nuxt-link
      href="/register"
      class="cursor-pointer rounded-2xl bg-gray-200 px-5 py-3 font-medium text-gray-800 transition duration-150 hover:bg-white"
    >
      Vyzkoušet zdarma
    </nuxt-link>

    <div class="mb-10 mt-[100px]">
      <img
        src="/img/dash_v1.png"
        class="h-auto max-w-full rounded-2xl border-gray-800 opacity-70 shadow-2xl shadow-blue-700 transition duration-500 hover:opacity-100"
      />
    </div>
  </div>
</template>

<style scoped></style>
