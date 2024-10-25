<template>
  <p
    class="text-base text-gray-500"
    v-if="
            organisation.expenses.length === 0 &&
            organisation.incomes.length === 0
          "
  >
    Organizace
  </p>
  <p
    class="mb-5 text-base text-gray-500"
    v-if="
            organisation.expenses.length > 0 &&
            organisation.incomes.length === 0
          "
  >
    Dodavatel
  </p>
  <p
    class="mb-5 text-base text-gray-500"
    v-if="
            organisation.expenses.length === 0 &&
            organisation.incomes.length > 0
          "
  >
    Zákazník
  </p>
  <p
    class="mb-5 text-base text-gray-500"
    v-if="
            organisation.expenses.length > 0 && organisation.incomes.length > 0
          "
  >
    Dodavatel a zákazník
  </p>
</template>

<script setup lang="ts">
  const props = defineProps(['organisation'])
</script>


<style scoped>

</style>