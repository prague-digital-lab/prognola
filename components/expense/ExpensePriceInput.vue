<script>
export default {
  props: ['expense'],

  data: () => {
    return {
      expanded: false,

      price: null,
    }
  },

  mounted() {
    this.price = this.expense.price
  }
  ,
  methods: {
    expand() {
      this.expanded = true
    },

    close() {
      this.expanded = false
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(2).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    async updatePrice() {
      this.expanded = false

      console.log('Updating')

      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses/' + this.expense.id, {
            method: 'PATCH',
            body: {
              price: this.price
            }
          })
      )
    }
  }
}
</script>

<template>
  <div class="relative">
    <p class="text-sm py-1 px-1 rounded text-gray-700 hover:bg-gray-100 mb-3" @click="expanded ? close() : expand()">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
           stroke="currentColor" class="size-5 inline-block me-2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
      </svg>

      {{ formatPrice(price) }} Kč
    </p>


    <Transition>
      <div v-if="expanded"
           class="absolute left-[-150px] top-[-5px] w-[147px] bg-white border-slate-200 border shadow rounded px-1 py-1">

        <input class="rounded w-full mb-2" type="number" step="0.01" autofocus v-on:blur="updatePrice" v-model="price">

        <p class="ms-2 text-sm text-gray-500">Měna</p>

        <p class="p-2 hover:bg-gray-100">Koruna (Kč)</p>
      </div>
    </Transition>

  </div>
</template>

<style scoped>

</style>