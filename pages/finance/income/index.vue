<template>
  <div>
    <div class="md:flex md:items-center md:justify-between mb-4">
      <div class="min-w-0 flex-1">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-4">Příjmy</h4>

        <p class="text-gray-500 text-sm">Celkem: {{ formatPrice(price_sum) }} Kč</p>
      </div>

      <div class="mt-4 flex md:ml-4 md:mt-0">
        <div class="me-2">
          <div class="mt-2">
            <select v-model="grouped_by"
                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
              <option :value="null">Seskupit</option>
              <option value="income_category">Kategorie</option>
            </select>
          </div>
        </div>

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

    <div class="border border-gray-200 rounded divide-gray-200 divide-y mb-4" v-if="grouped_by === null">
      <income-row v-for="income in incomes" :income="income"></income-row>

      <div v-if="incomes.length === 0" class="w-full flex items-center justify-center h-[400px]">
        <p class="text-gray-600">Žádné odpovídající příjmy.</p>
      </div>
    </div>

    <div v-if="grouped_by === 'income_category'" class="border border-gray-200 rounded mb-4">

      <div class="divide-gray-200 divide-y"
           v-for="category in incomes_by_category">

        <div class="w-full bg-slate-100 ps-3 py-2 text-sm text-gray-600">
          {{ category[0].income_category ? category[0].income_category.name : 'Příjmy bez kategorie' }}
        </div>
        <income-row v-for="income in category" :income="income"></income-row>
      </div>

      <div v-if="incomes.length === 0" class="w-full flex items-center justify-center h-[400px]">
        <p class="text-gray-600">Žádné odpovídající příjmy.</p>
      </div>
    </div>

    <div class="flex justify-end">
      <form @submit.prevent="createIncome">
        <input v-model="new_income_name"
               placeholder="Nový příjem..."
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
      // Page UI data
      loaded: false,
      new_income_name: '',

      // Data table params
      from: '2024-07-01',
      to: '2024-07-30',
      grouped_by: null,
      filter_category_id: null,
      filter_organisation_id: null,

      // Data
      incomes: [],
      price_sum: 0,
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

    console.log(localStorage.getItem('incomes.grouped_by'))
    if (localStorage.getItem('incomes.grouped_by')) {
      this.grouped_by = localStorage.getItem('incomes.grouped_by')
    } else {
      this.grouped_by = null
    }

    this.fetchData()
  },

  computed: {
    incomes_by_category() {
      return this.incomes.reduce(function (r, income) {
        r[income.income_category_id] = r[income.income_category_id] || [];
        r[income.income_category_id].push(income);
        return r;
      }, Object.create(null))
    }
  },

  watch: {
    from: function (newVal, oldVal) {
      this.fetchData()
      localStorage.setItem('from', newVal)
    },
    to: function (newVal, oldVal) {
      this.fetchData()
      localStorage.setItem('to', newVal)
    },
    grouped_by: function (newVal, oldVal) {
      localStorage.setItem('incomes.grouped_by', newVal)

      if (newVal === null) {
        localStorage.removeItem('incomes.grouped_by')
      }
    }
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('income', () =>
          client('/api/incomes', {
            method: 'GET',
            params: {
              from: this.from,
              to: this.to
            },
          })
      )

      this.incomes = data.value.data
      this.price_sum = data.value.price_sum

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    async createIncome() {

      const client = useSanctumClient();

      const {data} = await useAsyncData('income', () =>
          client('/api/incomes', {
            method: 'POST',
            body: {
              name: this.new_income_name,
              price: 0,
              paid_at: this.from
            }
          })
      )

      let id = data.value.id

      await navigateTo('/income/' + id)


    }
  }
}

</script>