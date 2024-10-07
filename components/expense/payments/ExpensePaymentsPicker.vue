<template>
  <div class="mb-4">
    <div class="flex items-center justify-between">
      <p class="mb-2 px-1 text-sm font-medium text-gray-500">Platby</p>
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
  },
};
</script>

<style scoped></style>
