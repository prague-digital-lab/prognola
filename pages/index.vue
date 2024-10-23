<script lang="ts">
export default {
  name: "index.vue",
};
</script>

<script setup lang="ts">
definePageMeta({
  layout: "guest",
});
</script>

<template>
  <div>
    <h1 class="mb-[40px] text-[40px] font-medium text-white">
      Budoucnost řízení firemních financí je tady.
    </h1>

    <p class="mb-[60px] text-[18px] text-gray-300">
      Představte si, že už nejste nervózní z toho, jak vaše firma v budoucnu
      vyjde s penězi. <br />
      Prognola je digitální finanční centrum nové generace. Pro firmy, kterým už
      nestačí znát zůstatek na firemním účtu.
    </p>

    <nuxt-link
      href="/register"
      class="cursor-pointer rounded-2xl bg-gray-200 px-5 py-3 font-medium text-gray-800 transition duration-150 hover:bg-white"
    >
      Vyzkoušet na 30 dní zdarma
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
