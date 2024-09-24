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
    <h1 class="text-[45px] mb-4 font-medium text-white">Prognola je budoucnost řízení vaší firmy.</h1>

    <p class="text-[20px] text-gray-400 font-medium">
      Už nemusíte přepínat mezi Slackem, Asanou a dalšími aplikacemi – a platit za každou zvlášť.
      <br>Řiďte projekty, komunikujte se zákazníky, spravujte finance a spolupracujte se svým týmem efektivně na jednom místě.
    </p>

    <img src="">

  </div>
</template>

<style scoped>

</style>