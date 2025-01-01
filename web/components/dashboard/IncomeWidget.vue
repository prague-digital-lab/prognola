<template>
  <div
    class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900"
  >
    <nuxt-link href="income">
      <div class="mb-3 flex items-center justify-between border-b border-gray-200 dark:border-zinc-800 pt-3 pb-2 px-4">
        <div class="truncate text-gray-600 dark:text-zinc-300">
          Příjmy <span class="text-gray-400">- tento měsíc</span>
        </div>
        <div class="text-xl font-semibold tracking-tight text-green-700 dark:text-green-500">
          {{ formatPrice(sum) }} Kč
        </div>
      </div>
    </nuxt-link>

    <div class="px-4 py-1">
    <p class="mb-2 text-sm text-zinc-500">Poslední uhrazené</p>
    </div>
  </div>
</template>

<script setup>
import { sumItemProp } from "~/lib/dexie/data_helpers.js";
import { DateTime } from "luxon";
import { getIncomesByPaidAt } from "~/lib/dexie/repository/income_repository.js";

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function filterPaid(items) {
  return items.filter((item) => {
    return item.payment_status === "paid";
  });
}

const sum = ref(0);
const range_from = ref();
const range_to = ref();

onMounted(async () => {
  range_from.value = DateTime.now().startOf("month");
  range_to.value = DateTime.now().endOf("month");

  let incomes = await getIncomesByPaidAt(
    range_from.value.toJSDate(),
    range_to.value.toJSDate(),
  );

  console.log("incomes", incomes);

  let incomes_paid = filterPaid(incomes);
  sum.value = sumItemProp(incomes_paid, "amount");
});
</script>

<style scoped></style>
