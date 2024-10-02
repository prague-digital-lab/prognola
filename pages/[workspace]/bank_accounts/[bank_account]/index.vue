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
            placeholder="Název účtu"
            v-model="input_name"
            v-on:blur="updateName"
          />

          <div>
            <bank-account-options :bank_account="bank_account" />
          </div>

          <!--          <div>-->
          <!--            &lt;!&ndash;            <bank_account-options :bank_account="bank_account"/>&ndash;&gt;-->
          <!--          </div>-->

          <div class="mt-4 flex md:ml-4 md:mt-0">
            <div class="me-2">
              <div class="mt-2">
                <input
                  type="date"
                  v-model="from"
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
                />
              </div>
            </div>

            <div>
              <div class="mt-2">
                <input
                  type="date"
                  v-model="to"
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
                />
              </div>
            </div>
          </div>
        </div>

        <p class="mb-5 text-base text-gray-500">Bankovní účet {{bank_account.account_number}}/{{bank_account.bank_number}} </p>

        <div
          class="mb-4 divide-y divide-gray-200 rounded border border-gray-200"
        >
          <bank-payment-row
            v-for="bank_payment in bank_payments"
            @click="navigateToPayment(bank_payment)"
            :bank_payment="bank_payment"
          />

          <div
            v-if="bank_payments.length === 0"
            class="flex h-[400px] w-full items-center justify-center"
          >
            <p class="text-base text-gray-600">Žádné odpovídající platby.</p>
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
import bank_payment from "~/pages/[workspace]/bank_payments/[bank_payment]/index.vue";

export default {
  data() {
    return {
      route: null,
      loaded: false,

      bank_account: null,
      bank_payments: [],
      input_name: "",
      input_description: "",

      from: "2024-07-01",
      to: "2024-07-30",
    };
  },

  mounted() {
    this.route = useRoute();

    if (localStorage.getItem("from")) {
      this.from = localStorage.getItem("from");
    } else {
      this.from = "2024-07-01";
    }

    if (localStorage.getItem("to")) {
      this.to = localStorage.getItem("to");
    } else {
      this.to = "2024-07-30";
    }

    this.fetchData();
  },

  watch: {
    from: function (newVal, oldVal) {
      this.fetchData();
      localStorage.setItem("from", newVal);
    },
    to: function (newVal, oldVal) {
      this.fetchData();
      localStorage.setItem("to", newVal);
    },
  },

  computed: {
    bank_payment() {
      return bank_payment;
    },
    title() {
      return this.bank_account
        ? this.bank_account.name + " - Prognola"
        : "Detail platby - Prognola";
    },
  },

  methods: {
    async navigateToPayment(payment) {
      await navigateTo("/bank_payments/" + payment.id);
    },

    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_account", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/bank_accounts/" +
            route.params.bank_account,
          {
            method: "GET",
          },
        ),
      );

      this.bank_account = data.value;
      this.input_name = data.value.name;

      const bank_payments_data = await useAsyncData("bank_payments", () =>
        client("/api/" + route.params.workspace + "/bank_payments", {
          method: "GET",
          params: {
            bank_account_id: route.params.bank_account,
            from: this.from,
            to: this.to,
          },
        }),
      );

      this.bank_payments = bank_payments_data.data.value;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(2).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async updateName() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_account", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/bank_accounts/" +
            route.params.bank_account,
          {
            method: "PATCH",
            body: {
              name: this.input_name,
            },
          },
        ),
      );
    },
  },
};
</script>
