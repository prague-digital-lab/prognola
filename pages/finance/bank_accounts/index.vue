<template>
  <div>
    <div class="md:flex md:items-center md:justify-between mb-4">
      <div class="min-w-0 flex-1">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-4">Účty</h4>
      </div>
    </div>

    <div class="border border-gray-200 rounded divide-gray-200 divide-y mb-4">
      <bank-account-row v-for="bank_account in bank_accounts" :bank_account="bank_account"></bank-account-row>

      <div v-if="bank_accounts.length === 0" class="w-full flex items-center justify-center h-[400px]">
        <p class="text-gray-600">Žádné účty.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      // Page UI data
      loaded: false,
      bank_accounts: [],
    }
  },

  mounted() {
    this.fetchData()
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('income', () =>
          client('/api/bank_accounts', {
            method: 'GET',
          })
      )

      this.bank_accounts = data.value

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    async createExpense() {

      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses', {
            method: 'POST',
            body: {
              description: this.new_expense_name,
              price: 0,
              paid_at: this.from
            }
          })
      )

      let id = data.value.id

      await navigateTo('/expenses/' + id)


    }
  }
}

</script>