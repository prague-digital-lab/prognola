<template>
  <div v-if="loaded">
    <div class="md:flex md:justify-between mb-4 divide-x divide-slate-100 h-auto">
      <div class="">
        <input type="text"
               class="p-0 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-3 border-none focus:ring-0"
               placeholder="Název výdaje" v-model="input_description">

        <p class="text-gray-500 text-sm">Výdaj</p>
      </div>

      <div class="w-[250px] ps-4">

        <p class="text-sm py-1 rounded text-gray-800 hover:bg-gray-100" v-if="expense.organisation">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor"
               class="size-4 inline-block me-2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
          </svg>

          {{ expense.organisation.name }}
        </p>

        <p class="text-sm py-1 rounded text-gray-600 hover:bg-gray-100" v-else>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor"
               class="size-4 inline-block me-2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
          </svg>

          zvolit organizaci
        </p>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      route: null,
      loaded: false,

      expense: null,
      input_description: null,
    }
  },

  mounted() {
    this.route = useRoute()

    this.fetchData()
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses/' + this.route.params.expense, {
            method: 'GET',
          })
      )

      this.expense = data.value
      this.input_description = data.value.description

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    }
  }
}

</script>