<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <h4
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            Nastavení účtu
          </h4>
        </div>
      </template>
    </page-content-header>

    <div
      class="mb-4 h-auto divide-x divide-slate-100 md:flex md:justify-between"
    ></div>
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
