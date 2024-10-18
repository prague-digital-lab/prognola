<template>
  <div>
    <div class="mb-4 md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Osoby a firmy
        </h4>
      </div>
    </div>

    <div
      v-if="organisations.length === 0"
      class="mb-4 flex h-[400px] w-full items-center justify-center rounded-md border border-gray-200"
    >
      <p class="text-gray-600">Žádné odpovídající organizace.</p>
    </div>

    <div
      class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 bg-white"
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
          class="me-2 rounded border border-gray-200 py-1 text-base"
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
useHead({
  title: "Organizace - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>

<script>
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
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisations", () =>
        client("/api/" + route.params.workspace + "/organisations", {
          method: "GET",
          params: {},
        }),
      );

      this.organisations = data.value.sort((a, b) => a.name.localeCompare(b.name));

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
