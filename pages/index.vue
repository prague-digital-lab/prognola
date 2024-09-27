<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "index.vue"
})
</script>

<script setup lang="ts">
definePageMeta({
  layout: 'guest',
})
</script>

<template>
  <div>
    <h1 class="text-[45px] mb-[40px] font-medium text-white">Plánujte finance jako profesionál</h1>

    <p class="text-[20px] text-gray-400 font-medium mb-[60px]">
      Získejte zpět přehled o aktuálním stavu financí ve vaší firmě.
      <br>Konečně budete mít prognózu, kolik budete mít peněz v nadcházejících obdobích.
    </p>

    <nuxt-link href="/register"
               class="px-5 py-3 bg-gray-200 hover:bg-white text-gray-800 rounded-2xl transition duration-150 font-medium cursor-pointer">
      Vyzkoušet zdarma
    </nuxt-link>

    <div class="mt-[100px] mb-10">
      <img src="/img/dash_v1.png"
           class="max-w-full h-auto rounded-2xl shadow-2xl shadow-blue-700 opacity-70 hover:opacity-100 transition duration-500 border-gray-800">
    </div>

  </div>
</template>

<style scoped>

</style>