<template>
  <div>
    <div class="rounded-md border border-zinc-800 py-[100px] mb-[100px] ">
      <h1 class="mb-[40px] text-[45px] font-semibold text-white text-center">
        Váš nástroj pro finanční řízení.<br>
        <span class="text-indigo-500 text-[45px]">Plánujte a řiďte firemní finance s lehkostí.</span>
      </h1>

      <p class="mb-[60px] text-[19px] text-zinc-400 leading-8 text-center">
        Seznamte se! Prognola je vaše nové finanční oddělení.<br />
        Převezměte kontrolu a řiďte firemní peníze s neskutečnou lehkostí.
    </p>

      <div class="flex justify-center">
    <nuxt-link
      href="/register"
      class="cursor-pointer rounded-2xl bg-gray-200 px-5 py-3 font-medium text-gray-800 transition duration-150 hover:bg-white"
    >
      Vyzkoušet nyní
    </nuxt-link>
      </div>
    </div>

    <div class="mb-10 mt-[100px]">
      <img
        src="/img/dash_v2.jpg"
        class="h-auto max-w-full rounded-2xl border-gray-800 opacity-100 shadow-xl shadow-blue-950 transition duration-500 hover:opacity-100"
      />
    </div>
  </div>
</template>
<script setup lang="ts">
definePageMeta({
  layout: "guest",
});
</script>

<style scoped></style>
