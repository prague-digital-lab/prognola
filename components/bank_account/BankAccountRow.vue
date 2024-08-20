<template>
  <div class="px-3 py-2 hover:bg-slate-50 flex justify-between items-center" @click="navigateToBankAccount">
    <div class="text-sm flex">
      <p class="text-gray-500 w-[60px] font-light">{{ bank_account.id }}</p>
      <p class="w-[400px]">{{ bank_account.name }}</p>
    </div>

    <div class="text-sm text-slate-600 font-light flex items-center">

    </div>
  </div>
</template>

<script>
import {defineComponent} from 'vue'
import {DateTime} from 'luxon'
import bank_accounts from "../../pages/bank_accounts/index.vue";

export default defineComponent({
  name: "BankAccountRow",
  computed: {
    bank_accounts() {
      return bank_accounts
    }
  },
  props: [
    'bank_account'
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

    async navigateToBankAccount() {
      await navigateTo('/bank_accounts/' + this.bank_account.id)
    }
  }
})
</script>

<style scoped>

</style>