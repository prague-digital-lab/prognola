<template>
  <div class="flex items-center justify-between px-3 py-2 hover:bg-slate-50">
    <div class="flex text-base">
      <p class="w-[60px] font-light text-gray-500">V-{{ expense.id }}</p>
      <p class="w-[400px]">
        {{ expense.description ? expense.description : "nový výdaj" }}
      </p>
    </div>

    <div class="flex items-center text-base font-light text-slate-600">
      <div
        v-if="expense.organisation"
        class="flex items-center rounded-[20px] border border-gray-200 px-3 py-1"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="me-2 inline-block size-4"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"
          />
        </svg>

        {{ expense.organisation.name }}
      </div>

      <p class="ps-2" v-if="expense.paid_at">
        {{ formatDate(expense.paid_at) }}
      </p>

      <div v-if="expense.payment_status === 'paid'" class="w-[120px]">
        <p class="ms-2 text-end">{{ formatPrice(expense.price) }} Kč</p>
      </div>

      <div v-else-if="expense.payment_status === 'draft'" class="w-[120px]">
        <p class="me-2 text-end font-semibold text-purple-800">ke zpracování</p>
      </div>

      <div v-else-if="expense.payment_status === 'plan'" class="w-[120px]">
        <p class="ms-2 text-end font-semibold text-yellow-500">
          ~{{ formatPrice(expense.price) }} Kč
        </p>
      </div>

      <div v-else class="w-[120px]">
        <p class="ms-2 text-end font-bold text-orange-400">
          {{ formatPrice(expense.price) }} Kč
        </p>
      </div>

      <div class="ms-2">
        <expense-status-icon :payment_status="expense.payment_status" />
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { DateTime } from "luxon";

export default defineComponent({
  props: ["expense"],
  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    formatDate(date) {
      let formatted = DateTime.fromISO(date);

      return formatted.toFormat("d.M.yyyy");
    },

    async navigateToExpense() {
      await navigateTo("/expenses/" + this.expense.id);
    },
  },
});
</script>

<style scoped></style>
