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
            class="mb-3 w-full border-none p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight"
            placeholder="Název příjmu"
            v-model="input_name"
            v-on:blur="updateName"
          />

          <div>
            <income-options :income="income" />
          </div>
        </div>

        <p class="mb-5 text-base text-gray-500">Příjem P-{{ income.id }}</p>

        <textarea
          v-model="input_description"
          class="mb-5 w-full border-none p-0 text-base text-slate-700 focus:ring-0"
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

        <p class="mb-2 px-1 text-xs font-medium text-gray-500">
          Kategorie příjmu
        </p>

        <income-category-picker :income="income" />

        <!--        <p class="text-xs px-1 font-medium text-gray-500 mb-2">Platby</p>-->

        <!--        <p class="text-xs py-1 px-1 rounded text-gray-800 hover:bg-gray-100 mb-1">-->
        <!--          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"-->
        <!--               class="size-5 text-gray-600 inline-block me-2">-->
        <!--            <path fill-rule="evenodd"-->
        <!--                  d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"-->
        <!--                  clip-rule="evenodd"/>-->
        <!--          </svg>-->

        <!--          -2000 Kč-->
        <!--        </p>-->

        <!--        <p class="text-xs py-1 px-1 rounded text-gray-800 hover:bg-gray-100 mb-4">-->
        <!--          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"-->
        <!--               class="size-5 text-gray-600 inline-block me-2">-->
        <!--            <path fill-rule="evenodd"-->
        <!--                  d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"-->
        <!--                  clip-rule="evenodd"/>-->
        <!--          </svg>-->

        <!--          -543 Kč-->
        <!--        </p>-->

        <p
          class="mb-2 px-1 text-xs font-medium text-gray-500"
          v-if="income.payment_status === 'paid'"
        >
          Uhrazeno
        </p>
        <p
          class="mb-2 px-1 text-xs font-medium text-gray-500"
          v-if="income.payment_status === 'plan'"
        >
          Plánovaná úhrada
        </p>
        <p
          class="mb-2 px-1 text-xs font-medium text-gray-500"
          v-if="income.payment_status === 'draft'"
        >
          Plánovaná úhrada
        </p>
        <p
          class="mb-2 px-1 text-xs font-medium text-gray-500"
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
