<template>
  <div>
    <div class="mb-[100px] rounded-md border border-zinc-800 py-[100px]">
      <div class="mb-[100px] flex justify-center">
        <div class="border-b border-zinc-700 pb-2 text-sm text-zinc-200">
          Prognola Plánování
        </div>
      </div>

      <h1 class="mb-[40px] text-center text-[45px] font-semibold text-white">
        Plánujte příjmy i výdaje.<br /><span class="text-indigo-500"
          >Snadno a rychle.</span
        >
      </h1>

      <p class="mb-[60px] text-center text-[18px] text-zinc-400">
        Nahlédněte do budoucnosti peněz vaší firmy.<br />
        Získejte představu jak se bude vyvíjet zůstatek na účtech.
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

    <div
      class="mb-[100px] grid grid-cols-2 divide-x divide-zinc-800 rounded-md border border-zinc-700"
    >
      <div class="p-10">
        <h2 class="mb-5 text-[25px] font-medium text-white">
          Nahlédněte do budoucnosti.
          <span class="text-indigo-500">Třeba hned!</span>
        </h2>
        <p class="text-[18px] text-gray-300">
          Už žádné zdlouhavé výpočty a přepisování dat do Excelu. Díky tomu, že
          Prognola zná současný peněžní zůstatek na bankovních účtech vaší
          firmy, to teď půjde kdykoli na jeden klik. To, jak se bude vyvíjet
          zůstatek na účtech si můžete zobrazit kdykoli.
        </p>
      </div>

      <div class="p-10">
        <h2 class="mb-5 text-[25px] font-medium text-white">
          Propojte plány s realitou.
        </h2>

        <p class="text-[18px] text-gray-300">
          Abyste při plánování nikdy neztratili pojem o realitě, Prognola chytře
          kombinuje skutečná data vaší firmy o uskutečněných příjmech a výdajích
          s budoucími plány. Řiďte vaši firmu podle reálných čísel, nyní můžete
          plánovat snadno a rychle.
        </p>
      </div>
    </div>

    <h2 class="mb-5 text-[25px] font-medium text-white">
      Řiďte firmu sebevědomě.
      <span class="text-indigo-500">Získejte zpět klid.</span>
    </h2>

    <p class="mb-[60px] text-[18px] text-gray-300">
      Zajímá vás jestli vám vyjdou peníze na blížící se výdaje? Nebo zvažujete
      další investice do vaší firmy? Moc dobře víme, že plánovat pouze pomocí
      zůstatku na účtu moc nejde. Nyní si můžete naplánovat budoucí odhady
      příjmů i výdajů. Zjistěte v reálném čase, zda vám plány vycházejí. Už
      nemusíte mít strach, jestli vaší firmě budou v následujících obdobích
      vycházet peníze.
    </p>

    <div class="mb-10 mt-[100px]">
      <img
        src="/img/dash_v2.jpg"
        class="h-auto max-w-full rounded-2xl border border-zinc-800 opacity-100 shadow-xl shadow-blue-950 transition duration-500 hover:opacity-100"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: "guest",
});

useHead({
  title: "Plánování - Plánujte příjmy a výdaje. Snadno a rychle.",
});
</script>

<style scoped></style>
