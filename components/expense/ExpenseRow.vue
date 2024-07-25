<template>
  <div class="px-3 py-2 hover:bg-slate-50 flex justify-between items-center" @click="navigateToExpense">
    <div class="text-sm flex">
      <p class="text-gray-500 w-[50px] font-light">V-{{ expense.id }}</p>
      <p class="w-[400px]">{{ expense.description }}</p>
    </div>

    <div class="text-sm text-slate-600 font-light flex items-center">
      <div v-if="expense.organisation" class="me-5 rounded-[20px] border border-gray-200 px-3 py-1 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-4 inline-block me-2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
        </svg>


        {{ expense.organisation.name }}
      </div>

      <p class="pe-2">{{ formatDate(expense.paid_at) }}</p>


      <div v-if="expense.payment_status === 'paid'" class="w-[120px]">
        <p class="text-end me-2 font-bold">
          {{ formatPrice(expense.price) }} Kč</p>
      </div>

      <div v-else class="w-[120px]">
        <p class="text-end text-red-800 font-bold me-2">{{ formatPrice(expense.price) }} Kč</p>
      </div>


      <div>
        <svg v-if="expense.payment_status === 'paid'" title="Uhrazeno" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block">
          <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
        </svg>

        <svg v-if="expense.payment_status === 'pending'" xmlns="http://www.w3.org/2000/svg" title="" fill="none"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>

        <svg v-if="expense.payment_status === 'plan'" xmlns="http://www.w3.org/2000/svg" title="" fill="none"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>

        <svg v-if="expense.payment_status === 'draft'" title="Koncept" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<script>
import {defineComponent} from 'vue'
import {DateTime} from 'luxon'

export default defineComponent({
  name: "ExpenseRow",
  props: [
    'expense'
  ],
  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    formatDate(date) {

      let formatted = DateTime.fromISO(date)

      return formatted.toFormat('d.M.yyyy')
    },

    async navigateToExpense() {
      await navigateTo('/expenses/' + this.expense.id)
    }
  }
})
</script>

<style scoped>

</style>