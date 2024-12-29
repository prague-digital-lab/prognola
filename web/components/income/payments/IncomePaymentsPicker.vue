<template>
  <div class="mb-4">
    <div class="flex items-center justify-between">
      <p class="mb-2 px-1 text-sm font-medium text-gray-500">Platby</p>
    </div>

    <income-paired-payment
      :payment="payment"
      @income-updated="$emit('income-updated')"
      v-for="payment in income.bank_payments"
    />

    <income-add-payment-options
      @open_pair_modal="openPairingModal"
    ></income-add-payment-options>

    <income-pair-with-payment-modal
      ref="modal_pair"
      @income-updated="$emit('income-updated')"
    ></income-pair-with-payment-modal>
  </div>
</template>

<script>
import IncomeAddPaymentOptions from "~/components/income/payments/IncomeAddPaymentOptions.vue";
import IncomePairWithPaymentModal from "~/components/income/payments/IncomePairWithPaymentModal.vue";
import IncomePairedPayment from "~/components/income/payments/IncomePairedPayment.vue";

export default {
  components: {
    IncomePairedPayment,
    IncomePairWithPaymentModal,
    IncomeAddPaymentOptions,
  },
  props: ["income"],

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
