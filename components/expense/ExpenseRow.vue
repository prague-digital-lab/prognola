<template>
  <div class="p-3 hover:bg-slate-50 flex justify-between">
    <div class="text-sm flex">
      <p class="text-gray-500 w-[50px] font-light">V-{{ expense.id }}</p>
      <p class="w-[400px]">{{ expense.description }}</p>
    </div>

    <div class="text-sm text-slate-600 font-light">
      {{expense.price}}
    </div>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "ExpenseRow",
  props: [
    'expense'
  ]
})
</script>

<style scoped>

</style>