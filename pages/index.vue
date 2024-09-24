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
    <h1 class="text-[45px] mb-[40px] font-medium text-white">Prognola je budoucnost řízení vaší firmy.</h1>

    <p class="text-[20px] text-gray-400 font-medium mb-[60px]">
      Už nemusíte přepínat mezi Slackem, Asanou a dalšími aplikacemi – a platit za každou zvlášť.
      <br>Řiďte projekty, komunikujte se zákazníky, spravujte finance a spolupracujte se svým týmem efektivně na jednom
      místě.
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