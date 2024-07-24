<template>
  <div class="p-3">
    {{expense.id}}
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