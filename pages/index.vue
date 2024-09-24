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
  <div class="container mx-[200px]">
    <h1 class="text-[45px] mb-4 font-medium text-white">Prognola je budoucnost řízení vaší firmy.<br>
      Budoucnost je právě dnes.
    </h1>

    <p class="text-[20px] text-gray-400 font-medium mb-10">
      Už nemusíte přepínat mezi Slackem, Asanou a dalšími aplikacemi – a platit za každou zvlášť.
      <br>Řiďte projekty, komunikujte se zákazníky, spravujte finance a spolupracujte se svým týmem efektivně na jednom místě.
    </p>

    <a class="px-5 py-3 bg-gray-200 hover:bg-white text-gray-800 rounded-2xl font-medium">Vyzkoušet zdarma</a>

    <img src="">

  </div>
</template>

<style scoped>

</style>