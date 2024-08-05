<template>
  <div>
    <div class="md:flex md:items-center md:justify-between mb-4">
      <div class="min-w-0 flex-1">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-4">Výdaje</h4>

        <p class="text-gray-600 text-sm">Výdaje celkem: {{formatPrice(price_sum)}} Kč</p>
      </div>
      <div class="mt-4 flex md:ml-4 md:mt-0">
        <div class="me-2">
          <div class="mt-2">
            <input type="date" v-model="from"
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
          </div>
        </div>

        <div>
          <div class="mt-2">
            <input type="date" v-model="to"
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
          </div>
        </div>
      </div>
    </div>

    <div class="border border-gray-200 rounded divide-gray-200 divide-y mb-4">
      <expense-row v-for="expense in expenses" :expense="expense"></expense-row>

      <div v-if="expenses.length === 0" class="w-full flex items-center justify-center h-[400px]">
        <p class="text-gray-600">Žádné odpovídající výdaje.</p>
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

<script>
export default {
  data() {
    return {
      loaded: false,

      from: '2024-07-01',
      to: '2024-07-30',

      expenses: [],
      price_sum: 0,

      new_expense_name: '',
    }
  },

  mounted() {
    if (localStorage.getItem('from')) {
      this.from = localStorage.getItem('from')
    } else {
      this.from = '2024-07-01';
    }

    if (localStorage.getItem('to')) {
      this.to = localStorage.getItem('to')
    } else {
      this.to = '2024-07-30';
    }

    this.fetchData()
  },

  watch: {
    from: function (newVal, oldVal) {
      this.fetchData()
      localStorage.setItem('from', newVal)
    },
    to: function (newVal, oldVal) {
      this.fetchData()
      localStorage.setItem('to', newVal)
    }
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('income', () =>
          client('/api/expenses', {
            method: 'GET',
            params: {
              from: this.from,
              to: this.to
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