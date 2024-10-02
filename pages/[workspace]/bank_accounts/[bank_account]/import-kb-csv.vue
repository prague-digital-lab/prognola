<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div>
    <div class="mb-4 md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Import plateb do účtu
        </h4>

        <p class="mb-5 text-base text-gray-700">
          Tento proces načte platby z CSV Komerční banky do aplikace.<br />
          CSV z KB bankovnictví obsahuje unikátní ID každé platby. Díky tomu
          aplikace zajistí, aby nevznikaly duplicitní platby.
        </p>
      </div>
    </div>

    <div v-if="!submitted">
      <form v-on:submit.prevent="uploadFile">
        <div class="mb-4">
          <input
            type="file"
            accept="text/csv"
            required
            v-on:change="loadFile($event)"
          />
        </div>

        <button
          type="submit"
          v-if="file"
          class="rounded bg-white px-2 py-1 text-base font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
        >
          Zahájit import
        </button>
      </form>
    </div>

    <div v-else>
      <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg
              class="h-5 w-5 text-green-400"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-3">
            <div class="mb-4 font-semibold">Import proběhl úspěšně!</div>

            <div class="mb-4">
              Nově nahrané platby: {{ created_payments_count }}<br />
              Aktualizované platby: {{ found_payments_count }}
            </div>

            <nuxt-link
              :href="'/bank_accounts/' + this.route.params.bank_account"
              type="button"
              class="rounded bg-white px-2 py-1 text-base font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
              >Přejít na výpis účtu</nuxt-link
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>

<script>
export default {
  data: () => {
    return {
      file: null,
      submitted: false,
      created_payments_count: 0,
      found_payments_count: 0,
    };
  },

  mounted() {
    this.route = useRoute();
  },

  computed: {
    title() {
      return "Import z CSV - Prognola";
    },
  },

  methods: {
    loadFile(event) {
      this.file = event.target.files[0];
    },

    async uploadFile() {
      const client = useSanctumClient();

      console.log(
        "/api/bank_accounts/" +
          this.route.params.bank_account +
          "/import/kb-csv",
      );

      const formData = new FormData();
      formData.append("file", this.file);

      const { data } = await useAsyncData("expense", () =>
        client(
          "/api/bank_accounts/" +
            this.route.params.bank_account +
            "/import/kb-csv",
          {
            method: "POST",
            body: formData,
          },
        ),
      );

      this.submitted = true;
      this.found_payments_count = data.value.found_payments_count;
      this.created_payments_count = data.value.created_payments_count;
    },
  },
};
</script>

<style scoped></style>
