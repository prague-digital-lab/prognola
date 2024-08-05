<template>
  <div>
    <div class="md:flex md:items-center md:justify-between mb-4">
      <div class="min-w-0 flex-1">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight">Výdaje</h4>
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

    <div class="border border-gray-200 rounded divide-gray-200 divide-y">
        <expense-row v-for="expense in expenses" :expense="expense"></expense-row>
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
    }
  },

  mounted() {
    this.fetchData()
  },

  watch: {
    from: function (newVal, oldVal) {
      this.fetchData()
    },
    to: function (newVal, oldVal) {
      this.fetchData()
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

      this.expenses = data.value

      this.whole_income = data.value.whole_income
      this.average_weekly = data.value.average_weekly
      this.average_daily = data.value.average_daily

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    }
  }
}

</script>