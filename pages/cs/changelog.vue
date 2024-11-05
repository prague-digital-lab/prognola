<template>
  <div>
    <div class="mb-10 pb-2 text-xl text-white">Nová vydání Prognoly</div>

    <div class="grid grid-cols-3 border-t border-zinc-700 pt-10 p-5">
      <div>
        <p class="text-zinc-400">5. listopadu 2024</p>
      </div>
      <div class=" flex flex-col space-y-10 col-span-2 text-zinc-100 leading-8 font-light">
        <div>
          <h1 class="text-xl text-white mb-4 font-normal">Vylepšení</h1>
          <p class="text-zinc-200">PRO-408 - Spárované platby v detailu výdaje se nově řadí podle toho kdy byly
            uhrazeny.</p>
        </div>

        <div>
          <h1 class="text-xl text-white mb-4 font-normal">Opravy chyb</h1>

          <p>PRO-438 - Při otevření Prognoly se neotevřela správná organizace.</p>
          <p>PRO-410 - V některých případech nebylo možné zobrazit výdaje organizace.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: "guest"
});

useHead({
  title: "Nová vydání"
});
</script>

<style scoped></style>
