<template>
  <div>
    <page-content-header>
      <template v-slot:title>
        <heading>Schránka</heading>
      </template>
    </page-content-header>

    <div
      class="mb-4 h-auto divide-y divide-gray-200 rounded border border-gray-200 bg-white dark:divide-zinc-800 dark:border-zinc-800"
    >
      <nuxt-link
        :href="'/' + route.params.workspace + '/inbox/expense_drafts'"
        class="block cursor-default"
      >
        <div
          class="px-3 py-2 text-base text-gray-700 hover:bg-gray-hover dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
        >
          Výdaje ke zpracování
        </div>
      </nuxt-link>

      <nuxt-link
        :href="'/' + route.params.workspace + '/inbox/payments_to_pair'"
        class="block cursor-default"
      >
        <div
          class="px-3 py-2 text-base text-gray-700 hover:bg-gray-hover dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
        >
          Platby ke spárování
        </div>
      </nuxt-link>
    </div>
  </div>
</template>

<script setup>
import Heading from "~/components/ui/Heading.vue";
import PageContentHeader from "~/components/ui/PageContentHeader.vue";

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
