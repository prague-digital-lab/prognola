<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <div
      class="mb-4 h-auto divide-x divide-slate-100 md:flex md:justify-between"
    >
      <div class="me-5 w-full">
        <div class="flex justify-between">
          <input
            type="text"
            class="mb-3 w-full border-none bg-transparent p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight"
            placeholder="Název příjmu"
            v-model="input_name"
            v-on:blur="updateName"
          />

          <div>
            <income-options :income="income" />
          </div>
        </div>

        <p class="mb-5 text-base text-gray-500">Příjem</p>

        <textarea
          v-model="input_description"
          class="mb-5 w-full border-none bg-transparent p-0 text-base text-slate-700 focus:ring-0"
          placeholder="Přidat popis..."
          v-on:blur="updateDescription"
        ></textarea>

        <!--        <p class="mb-2 text-base text-gray-600">Doklady</p>-->
      </div>

      <div class="w-[250px] ps-4">
        <income-price-input :income="income" />

        <income-status-select :income="income" />

        <p
          class="mb-4 rounded px-1 py-1 text-base text-gray-700 hover:bg-gray-100"
          v-if="income.organisation"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="me-2 inline-block size-5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"
            />
          </svg>

          {{ income.organisation.name }}
        </p>

        <p
          class="mb-7 rounded px-1 py-1 text-base text-gray-500 hover:bg-gray-100"
          v-else
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="me-2 inline-block size-5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"
            />
          </svg>

          zvolit organizaci
        </p>

        <p class="mb-2 px-1 text-sm font-medium text-gray-500">
          Kategorie příjmu
        </p>

        <income-category-picker :income="income" />

        <income-payments-picker :income="income" />

        <p
          class="mb-2 px-1 text-sm font-medium text-gray-500"
          v-if="income.payment_status === 'paid'"
        >
          Uhrazeno
        </p>
        <p
          class="mb-2 px-1 text-sm font-medium text-gray-500"
          v-if="income.payment_status === 'plan'"
        >
          Plánovaná úhrada
        </p>
        <p
          class="mb-2 px-1 text-sm font-medium text-gray-500"
          v-if="income.payment_status === 'draft'"
        >
          Plánovaná úhrada
        </p>
        <p
          class="mb-2 px-1 text-sm font-medium text-gray-500"
          v-if="income.payment_status === 'pending'"
        >
          Plánovaná úhrada
        </p>

        <income-paid-at-input :income="income" />
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
import IncomeStatusSelect from "~/components/income/IncomeStatusSelect.vue";
import IncomePaidAtInput from "~/components/income/IncomePaidAtInput.vue";

export default {
  components: { IncomePaidAtInput, IncomeStatusSelect },
  data() {
    return {
      route: null,
      loaded: false,

      income: null,
      input_name: "",
      input_description: "",
    };
  },

  mounted() {
    this.route = useRoute();

    this.fetchData();
  },

  computed: {
    title() {
      return this.income
        ? this.income.name + " - Prognola"
        : "Detail příjmu - Prognola";
    },
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/incomes/" +
            this.route.params.income,
          {
            method: "GET",
          },
        ),
      );

      this.income = data.value;
      this.input_name = data.value.name;
      this.input_description = data.value.description;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async updateName() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client(
          "/api/" + route.params.workspace + "/incomes/" + this.income.uuid,

          {
            method: "PATCH",
            body: {
              name: this.input_name,
            },
          },
        ),
      );
    },

    async updateDescription() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client(
          "/api/" + route.params.workspace + "/incomes/" + this.income.uuid,
          {
            method: "PATCH",
            body: {
              description: this.input_description,
            },
          },
        ),
      );
    },
  },
};
</script>
