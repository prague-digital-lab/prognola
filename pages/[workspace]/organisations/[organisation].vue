<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <input
          type="text"
          class="w-full border-none bg-transparent p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight dark:text-zinc-200"
          placeholder="Název"
          v-model="input_name"
          v-on:blur="updateName"
        />
      </template>
      <template v-slot:subtitle>
        <organisation-label :organisation="organisation"></organisation-label>
      </template>
    </page-content-header>

    <div class="mb-4 md:flex md:justify-between">
      <div class="me-5 w-full">
        <textarea
          v-model="input_internal_note"
          class="mb-5 w-full border-none bg-transparent p-0 text-base text-slate-700 focus:ring-0 dark:text-zinc-300"
          placeholder="Přidat popis..."
          v-on:blur="updateInternalNote"
        ></textarea>

        <p class="mb-2 text-gray-700 dark:text-zinc-400">Fakturační údaje</p>

        <organisation-data :organisation="organisation"></organisation-data>

        <organisation-expenses
          :organisation="organisation"
        ></organisation-expenses>
      </div>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>

<script>
export default {
  data() {
    return {
      route: null,
      loaded: false,

      organisation: null,
      input_name: "",
      input_internal_note: "",
    };
  },

  mounted() {
    this.route = useRoute();

    this.fetchData();
  },

  computed: {
    title() {
      return this.organisation
        ? this.organisation.name + " - Prognola"
        : "Detail organizace - Prognola";
    },
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisation", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/organisations/" +
            route.params.organisation,
          {
            method: "GET",
          },
        ),
      );

      this.organisation = data.value;
      this.input_name = data.value.name;
      this.input_internal_note = data.value.internal_note;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async updateName() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisation", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/organisations/" +
            route.params.organisation,
          {
            method: "PATCH",
            body: {
              name: this.input_name,
            },
          },
        ),
      );
    },

    async updateInternalNote() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisation", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/organisations/" +
            route.params.organisation,
          {
            method: "PATCH",
            body: {
              internal_note: this.input_internal_note,
            },
          },
        ),
      );
    },
  },
};
</script>
