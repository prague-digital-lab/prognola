<template>
  <div class="p-3 hover:bg-slate-50 flex justify-between">
    <div class="text-sm flex">
      <p class="text-gray-500 w-[50px] font-light">V-{{ expense.id }}</p>
      <p class="w-[400px]">{{ expense.description }}</p>
    </div>

    <div class="text-sm text-slate-600 font-light flex">
      <p class="pe-2">{{expense.paid_at}}</p>
      <p class="w-[70px] text-end">{{ formatPrice(expense.price) }} Kč</p>
    </div>
  </div>
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
  name: "ExpenseRow",
  props: [
    'expense'
  ],
  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    }
  }
})
</script>

<style scoped>

</style>