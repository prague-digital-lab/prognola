<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <div
      class="mb-4 h-screen divide-x divide-slate-100 md:flex md:justify-between"
    >
      <div class="me-5 w-full overflow-scroll">
        <div class="flex justify-between">
          <input
            type="text"
            class="mb-3 w-full border-none p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight"
            placeholder="Název"
            v-model="input_description"
            v-on:blur="updateDescription"
          />

          <div>
            <!--            <organisation-options :organisation="organisation"/>-->
          </div>
        </div>

        <p class="mb-5 text-sm text-gray-500">{{ organisation.id }}</p>

        <textarea
          v-model="input_internal_note"
          class="mb-5 w-full border-none p-0 text-sm text-slate-700 focus:ring-0"
          placeholder="Přidat popis..."
          v-on:blur="updateInternalNote"
        ></textarea>

        <p class="mb-2 text-sm text-gray-600">Výdaje</p>

        <div class="divide-y divide-slate-200 rounded border border-slate-200">
          <expense-row
            :expense="expense"
            v-for="expense in organisation.received_invoices"
          ></expense-row>

          <div>
            <a
              :href="
                'https://valasskapevnost.cz/admin/crm/organisations/' +
                organisation.id
              "
              target="_blank"
            >
              <div class="w-full px-5 py-3 text-sm text-slate-600">
                Zobrazit v IS
              </div>
            </a>
          </div>
        </div>
      </div>

      <div class="w-[250px] ps-4">
        <!--        <organisation-price-input :organisation="organisation"/>-->

        <!--        <organisation-status-select :organisation="organisation"/>-->

        <!--        <organisation-organisation-picker :organisation="organisation"/>-->

        <!--        <p class="text-xs px-1 font-medium text-gray-500 mb-2">Kategorie výdaje</p>-->

        <!--        <organisation-category-picker :organisation="organisation"/>-->

        <!--        <organisation-payments-picker :organisation="organisation"/>-->

        <!--        <p class="text-xs px-1 font-medium text-gray-500 mb-2">Datum přijetí</p>-->
        <!--        <organisation-received-at-input :organisation="organisation"/>-->

        <!--        <p class="text-xs px-1 font-medium text-gray-500 mb-2" v-if="organisation.payment_status === 'paid'">Uhrazeno</p>-->
        <!--        <p class="text-xs px-1 font-medium text-gray-500 mb-2" v-if="organisation.payment_status === 'plan'">Plánovaná-->
        <!--          úhrada</p>-->
        <!--        <p class="text-xs px-1 font-medium text-gray-500 mb-2" v-if="organisation.payment_status === 'draft'">Plánovaná-->
        <!--          úhrada</p>-->
        <!--        <p class="text-xs px-1 font-medium text-gray-500 mb-2" v-if="organisation.payment_status === 'pending'">Plánovaná-->
        <!--          úhrada</p>-->

        <!--        <organisation-paid-at-input :organisation="organisation"/>-->
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
  data() {
    return {
      route: null,
      loaded: false,

      organisation: null,
      input_description: "",
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

      const { data } = await useAsyncData("organisation", () =>
        client("/api/organisations/" + this.route.params.organisation, {
          method: "GET",
        }),
      );

      this.organisation = data.value;
      this.input_description = data.value.name;
      this.input_internal_note = data.value.internal_note;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async updateDescription() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("organisation", () =>
        client("/api/organisations/" + this.route.params.organisation, {
          method: "PATCH",
          body: {
            description: this.input_description,
          },
        }),
      );
    },

    async updateInternalNote() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("organisation", () =>
        client("/api/organisations/" + this.route.params.organisation, {
          method: "PATCH",
          body: {
            internal_note: this.input_internal_note,
          },
        }),
      );
    },
  },
};
</script>
