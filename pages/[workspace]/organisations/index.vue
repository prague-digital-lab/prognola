<template>
  <div>
    <page-content-header>
      <template v-slot:title>
        <heading>Osoby a firmy</heading>
      </template>
    </page-content-header>

    <div
      v-if="organisations.length === 0"
      class="mb-4 flex h-[400px] w-full items-center justify-center rounded-md border border-gray-200 dark:border-zinc-800"
    >
      <p class="text-gray-600 dark:text-zinc-400">
        Žádné odpovídající organizace.
      </p>
    </div>

    <div
      class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 bg-white dark:divide-zinc-800 dark:border-zinc-800"
      v-if="organisations.length > 0"
    >
      <organisation-row
        v-for="organisation in organisations"
        :organisation="organisation"
      ></organisation-row>
    </div>

    <div class="flex justify-end">
      <form @submit.prevent="createOrganisation">
        <input
          v-model="new_organisation_name"
          placeholder="Nová organizace..."
          required
          class="me-2 rounded border border-gray-200 py-1 text-base dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200"
        />

        <button
          type="submit"
          class="rounded bg-indigo-700 px-3 py-1 text-gray-100 transition hover:bg-indigo-900"
        >
          Přidat
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import Heading from "~/components/ui/Heading.vue";

useHead({
  title: "Organizace - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>

<script>
import { getIncomesByPaidAt } from "~/lib/dexie/repository/income_repository.js";
import { getOrganisations } from "~/lib/dexie/repository/organisation_repository.js";

export default {
  data() {
    return {
      // Page UI data
      loaded: false,
      new_organisation_name: "",

      // Data table params
      grouped_by: null,
      filter_category_id: null,
      filter_organisation_id: null,

      // Data
      organisations: [],
    };
  },

  mounted() {
    this.fetchData();
  },

  methods: {
    async fetchData() {
      this.organisations = await getOrganisations();

      this.organisations = this.organisations.sort((a, b) =>
        a.name.localeCompare(b.name),
      );

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async createOrganisation() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisation", () =>
        client("/api/" + route.params.workspace + "/organisations", {
          method: "POST",
          body: {
            name: this.new_organisation_name,
          },
        }),
      );

      let uuid = data.value.uuid;

      await navigateTo("/" + route.params.workspace + "/organisations/" + uuid);
    },
  },
};
</script>
