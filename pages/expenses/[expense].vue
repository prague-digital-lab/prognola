<template>
  <div>
    <div class="md:flex md:items-center md:justify-between mb-4" v-if="loaded">
      <div class="min-w-0 flex-1">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight">{{expense.description}}</h4>
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
          client('/api/expenses/'+ this.route.params.expense, {
            method: 'GET',
          })
      )

      this.expense = data.value

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    }
  }
}

</script>