<script>
export default {
  props: ["expense"],

  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(2).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async navigateToPayment(payment) {
      await navigateTo("/bank_payments/" + payment.uuid);
    },
  },
};
</script>

<template>
  <div class="mb-4">
    <div class="flex items-center justify-between">
      <p class="px-1 text-xs font-medium text-gray-500">Platby</p>

      <!--      <nuxt-link :href="'/bank_payments/' + expense.id + '/pair'">+</nuxt-link>-->
    </div>

    <p
      class="mb-1 rounded px-1 py-1 text-xs text-gray-800 hover:bg-gray-100"
      v-for="payment in expense.bank_payments"
      @click="navigateToPayment(payment)"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="currentColor"
        class="me-2 inline-block size-5 text-gray-600"
      >
        <path
          fill-rule="evenodd"
          d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"
          clip-rule="evenodd"
        />
      </svg>

      {{ formatPrice(payment.amount) }} Kč
    </p>
  </div>
</template>

<style scoped></style>
