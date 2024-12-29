<template>
  <div>
    <page-content-header>
      <template v-slot:title>
        <heading>Platby ke spárování</heading>
      </template>
      <template v-slot:subtitle>
        <p class="text-base text-gray-500 dark:text-zinc-400" v-if="loaded">
          Celkem plateb ke spárování: {{ payments.length }}
        </p>
      </template>
    </page-content-header>

    <div
      class="dark: mb-4 divide-y divide-gray-200 dark:divide-zinc-800 rounded border border-gray-200 dark:border-zinc-800"
      v-if="loaded"
    >
      <bank-payment-row
        v-for="payment in payments"
        :bank_payment="payment"
        @click="pairPayment(payment)"
      />

      <div
        v-if="payments.length === 0"
        class="flex h-[400px] w-full items-center justify-center bg-white"
      >
        <p class="text-center text-base text-gray-500">
          Všechny úhrady jsou spárované. <br />Tak se to musí!
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import Heading from "~/components/ui/Heading.vue";
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
      // Page UI data
      loaded: false,
      new_expense_name: "",

      // Data table params
      grouped_by: null,
      filter_category_id: null,
      filter_organisation_id: null,

      // Data
      payments: [],
    };
  },

  mounted() {
    this.fetchData();
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_payments_to_pair", () =>
        client("/api/" + route.params.workspace + "/bank_payments", {
          method: "GET",
          params: {
            from: "2024-01-01",
            is_paired: false,
            type: "expense",
          },
        }),
      );

      // const income_data = await useAsyncData("bank_payments_to_pair", () =>
      //   client("/api/" + route.params.workspace + "/bank_payments", {
      //     method: "GET",
      //     params: {
      //       from: "2024-01-01",
      //       is_paired: false,
      //       type: "income",
      //     },
      //   }),
      // );

      this.payments = data.value;
      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async pairPayment(payment) {
      const route = useRoute();
      await navigateTo(
        "/" +
          route.params.workspace +
          "/bank_payments/" +
          payment.uuid +
          "/pair",
      );
    },
  },
};
</script>
