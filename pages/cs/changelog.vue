<template>
  <div>
    <div class="mb-10 pb-2 text-xl text-white">Nová vydání Prognoly</div>

    <div class="grid grid-cols-3 border-t border-zinc-700 p-5 pt-10">
      <div>
        <p class="text-zinc-400">5. listopadu 2024</p>
      </div>
      <div
        class="col-span-2 flex flex-col space-y-10 font-light leading-8 text-zinc-100"
      >
        <div>
          <h1 class="mb-4 text-xl font-normal text-white">Vylepšení</h1>
          <p>
            PRO-408 - Spárované platby v detailu výdaje se nově řadí podle toho
            kdy byly uhrazeny.
          </p>
          <p>PRO-449 - Je možné smazat nahrané přílohy výdaje.</p>
        </div>

        <div>
          <h1 class="mb-4 text-xl font-normal text-white">Opravy chyb</h1>

          <p>
            PRO-438 - Při otevření Prognoly se neotevřela správná organizace.
          </p>
          <p>
            PRO-410 - V některých případech nebylo možné zobrazit výdaje
            organizace.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: "guest",
});

useHead({
  title: "Co je nového v aplikaci",
});
</script>

<style scoped></style>
