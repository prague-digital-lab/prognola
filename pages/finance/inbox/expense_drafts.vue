<template>
  <div>
    <div class="md:flex md:items-center md:justify-between mb-4">
      <div class="min-w-0 flex-1">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-4">Výdaje ke zpracování</h4>

        <p class="text-gray-500 text-sm">Celkem výdajů ke zpracování: {{expenses.length}}</p>
      </div>
    </div>

    <div class="border border-gray-200 rounded divide-gray-200 divide-y mb-4" v-if="grouped_by === null">
      <expense-row v-for="expense in expenses" :expense="expense"></expense-row>

      <div v-if="expenses.length === 0" class="w-full flex items-center justify-center h-[400px]">
        <p class="text-gray-600">Všechny výdaje jsou zpracované. ✅<br>Tak se to musí!</p>
      </div>
    </div>

    <div class="flex justify-end">
      <form @submit.prevent="createExpense">
        <input v-model="new_expense_name"
               placeholder="Nový výdaj..."
               required
               class="rounded border border-gray-200 me-2 py-1">

        <button type="submit" class="bg-indigo-700 hover:bg-indigo-900 transition rounded px-3 py-1 text-gray-100">
          Přidat
        </button>
      </form>
    </div>


  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default',
  middleware: ['sanctum:auth', 'sanctum:verified'],
})
</script>

<script>
export default {
  data() {
    return {
      // Page UI data
      loaded: false,
      new_expense_name: '',

      // Data table params
      grouped_by: null,
      filter_category_id: null,
      filter_organisation_id: null,

      // Data
      expenses: [],
      price_sum: 0,
    }
  },

  mounted() {
    this.fetchData()
  },

  computed: {
    expenses_by_category() {
      return this.expenses.reduce(function (r, expense) {
        r[expense.expense_category_id] = r[expense.expense_category_id] || [];
        r[expense.expense_category_id].push(expense);
        return r;
      }, Object.create(null))
    }
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('income', () =>
          client('/api/expenses', {
            method: 'GET',
            params: {
              payment_status: 'draft'
            },
          })
      )

      this.expenses = data.value.data
      this.price_sum = data.value.price_sum

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