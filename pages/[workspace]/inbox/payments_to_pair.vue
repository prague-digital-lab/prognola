<template>
  <div>
    <div class="mb-4 md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Platby ke spárování
        </h4>

        <p class="text-sm text-gray-500" v-if="loaded">
          Celkem plateb ke spárování: {{ payments.length }}
        </p>
      </div>
    </div>

    <div
      class="mb-4 divide-y divide-gray-200 rounded border border-gray-200"
      v-if="loaded"
    >
      <bank-payment-row
        v-for="payment in payments"
        :bank_payment="payment"
        @click="pairPayment(payment)"
      />

      <div
        v-if="payments.length === 0"
        class="flex h-[400px] w-full items-center justify-center"
      >
        <p class="text-center text-gray-600">
          Všechny úhrady jsou spárované. ✅ <br />Tak se to musí!
        </p>
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
            not_paired: true,
            type: "expense",
          },
        }),
      );

      this.payments = data.value;
      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async pairPayment(payment) {
      await navigateTo("/bank_payments/" + payment.uuid + "/pair");
    },
  },
};
</script>
