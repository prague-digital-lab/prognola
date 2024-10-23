<template>
  <div>
    <div class="mb-4 md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Schránka
        </h4>
      </div>
    </div>

    <div
      class="mb-4 h-auto divide-y divide-gray-200 rounded border border-gray-200 bg-white"
    >
      <nuxt-link
        :href="'/' + route.params.workspace + '/inbox/expense_drafts'"
        class="block cursor-default"
      >
        <div
          class="px-3 py-2 text-base text-gray-700 duration-100 hover:bg-gray-hover"
        >
          Výdaje ke zpracování
        </div>
      </nuxt-link>

      <nuxt-link
        :href="'/' + route.params.workspace + '/inbox/payments_to_pair'"
        class="block cursor-default"
      >
        <div
          class="px-3 py-2 text-base text-gray-700 duration-100 hover:bg-gray-hover"
        >
          Platby ke spárování
        </div>
      </nuxt-link>
    </div>
  </div>
</template>

<script setup>
useHead({
  title: "Ke zpracování - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const route = useRoute();
</script>

<script>
export default {};
</script>
