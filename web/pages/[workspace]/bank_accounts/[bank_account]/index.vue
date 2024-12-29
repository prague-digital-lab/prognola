<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <input
            type="text"
            class="w-full border-none bg-transparent p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight dark:text-zinc-200"
            placeholder="Název účtu"
            v-model="input_name"
            v-on:blur="updateName"
          />

          <bank-account-options :bank_account="bank_account" />
        </div>
      </template>
      <template v-slot:subtitle>
        <div class="mb-5">
          <span
            class="me-2 rounded-md border border-gray-300 bg-white px-[10px] py-1 text-sm text-gray-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
          >
            Číslo účtu: {{ bank_account.account_number }}/{{
              bank_account.bank_number
            }}
          </span>

          <span
            class="me-2 rounded-md border border-gray-300 bg-white px-[10px] py-1 text-sm text-gray-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
            v-if="bank_account.synced_at"
          >
            <span v-if="bank_account.bank === 'fio'">Platby staženy</span>
            <span v-if="bank_account.bank === 'komercni_banka_csv'"
              >Importováno z CSV do</span
            >
            {{ formatDate(bank_account.synced_at) }}
          </span>
        </div>
      </template>
      <template v-slot:controls>
        <div class="flex md:mt-0">
          <div class="me-2">
            <input
              type="date"
              v-model="from"
              class="border-1 block w-full rounded-md border-gray-200 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
            />
          </div>

          <div>
            <input
              type="date"
              v-model="to"
              class="border-1 block w-full rounded-md border-gray-200 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
            />
          </div>
        </div>
      </template>
    </page-content-header>

    <div
      class="mb-4 h-auto divide-x divide-slate-100 md:flex md:justify-between"
    >
      <div class="w-full">
        <div
          class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
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
            <p class="text-base text-gray-600 dark:text-zinc-400">
              Žádné odpovídající platby.
            </p>
          </div>
        </div>
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
import bank_payment from "~/pages/[workspace]/bank_payments/[bank_payment]/index.vue";
import { DateTime } from "luxon";

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

    this.fetchData().then(() => {
      this.loaded = true;
      this.fetchPayments();
    });
  },

  watch: {
    from: function (newVal, oldVal) {
      this.fetchPayments();
      localStorage.setItem("from", newVal);
    },
    to: function (newVal, oldVal) {
      this.fetchPayments();
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
      const route = useRoute();
      await navigateTo(
        "/" + route.params.workspace + "/bank_payments/" + payment.uuid,
      );
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
    },

    async fetchPayments() {
      const client = useSanctumClient();
      const route = useRoute();

      const bank_payments_data = await useAsyncData("bank_payments", () =>
        client("/api/" + route.params.workspace + "/bank_payments", {
          method: "GET",
          params: {
            bank_account: route.params.bank_account,
            from: this.from,
            to: this.to,
          },
        }),
      );

      this.bank_payments = bank_payments_data.data.value;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(2).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    formatDate(date) {
      let formatted = DateTime.fromISO(date);

      return formatted.toFormat("d.M.yyyy");
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
