<template>
  <p
    class="text-base text-gray-500"
    v-if="expenses.length === 0 && incomes.length === 0"
  >
    Organizace
  </p>
  <p
    class="mb-5 text-base text-gray-500"
    v-if="expenses.length > 0 && incomes.length === 0"
  >
    Dodavatel
  </p>
  <p
    class="mb-5 text-base text-gray-500"
    v-if="expenses.length === 0 && incomes.length > 0"
  >
    Zákazník
  </p>
  <p
    class="mb-5 text-base text-gray-500"
    v-if="expenses.length > 0 && incomes.length > 0"
  >
    Dodavatel a zákazník
  </p>
</template>

<script setup lang="ts">
import { getExpensesByOrganisation } from "~/lib/dexie/repository/expense_repository";
import { getIncomesByOrganisation } from "~/lib/dexie/repository/income_repository";

const props = defineProps(["organisation"]);

const expenses = ref([]);
const incomes = ref([]);

onMounted(async () => {
  expenses.value = await getExpensesByOrganisation(props.organisation.uuid);
  incomes.value = await getIncomesByOrganisation(props.organisation.uuid);
});
</script>

<style scoped></style>
