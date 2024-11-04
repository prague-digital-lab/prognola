<template>
  <div>
    <div class="mb-[100px] rounded-md border border-zinc-800 py-[100px]">
      <h1 class="mb-[40px] text-center text-[45px] font-semibold text-white">
        Váš nástroj pro finanční řízení.<br />
        <span class="text-[45px] text-indigo-500"
          >Plánujte firemní finance s lehkostí.</span
        >
      </h1>

      <p class="mb-[60px] text-center text-[19px] leading-8 text-zinc-400">
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
