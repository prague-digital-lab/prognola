<template>
  <div class="mb-4">
    <div class="flex items-center justify-between">
      <p class="mb-2 px-1 text-sm font-medium text-gray-500">Platby</p>
    </div>

    <div
      class="mb-2 rounded-md border border-gray-200 bg-white px-3 py-1 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"
    >
      <p class="text-sm text-gray-400">Σ {{ formatPrice(paymentSum) }} Kč</p>
    </div>

    <expense-paired-payment
      :payment="payment"
      @expense-updated="$emit('expense-updated')"
      v-for="payment in expense.bank_payments"
    />

    <expense-add-payment-options
      @open_pair_modal="openPairingModal"
    ></expense-add-payment-options>

    <expense-pair-with-payment-modal
      :expense="expense"
      ref="modal_pair"
      @expense-updated="$emit('expense-updated')"
    ></expense-pair-with-payment-modal>
  </div>
</template>

<script>
import ExpenseAddPaymentOptions from "~/components/expense/payments/ExpenseAddPaymentOptions.vue";
import ExpensePairWithPaymentModal from "~/components/expense/payments/ExpensePairWithPaymentModal.vue";
import ExpensePairedPayment from "~/components/expense/payments/ExpensePairedPayment.vue";

export default {
  components: {
    ExpensePairedPayment,
    ExpensePairWithPaymentModal,
    ExpenseAddPaymentOptions,
  },
  props: ["expense"],

  data: () => {
    return {};
  },

  methods: {
    openPairingModal() {
      this.$refs.modal_pair.openModal();
    },

    async navigateToPayment(payment) {
      const route = useRoute();
      await navigateTo(
        "/" + route.params.workspace + "/bank_payments/" + payment.uuid,
      );
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(2).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },
  },

  computed: {
    paymentSum() {
      const payments = this.expense.bank_payments;

      return payments.reduce((acc, o) => acc + parseInt(o.amount), 0);
    },
  },
};
</script>

<style scoped></style>
