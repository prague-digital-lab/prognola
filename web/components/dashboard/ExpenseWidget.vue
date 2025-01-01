<template>
  <div
    class="overflow-hidden rounded-lg border border-gray-200 bg-white px-3 py-4 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900"
  >
    <nuxt-link href="expenses">
      <dt class="truncate text-gray-500 dark:text-zinc-400">
        Uhrazené výdaje <span class="text-gray-400">- tento měsíc</span>
      </dt>
    </nuxt-link>
    <dd class="mt-1 text-xl font-semibold tracking-tight text-red-600">
      {{ formatPrice(sum) }} Kč
    </dd>
  </div>
</template>

<script setup>
import { sumItemProp } from "~/lib/dexie/data_helpers.js";
import { DateTime } from "luxon";
import { getIncomesByPaidAt } from "~/lib/dexie/repository/income_repository.js";
import {getExpensesByPaidAt} from "~/lib/dexie/repository/expense_repository.js";

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

  let expenses = await getExpensesByPaidAt(
    range_from.value.toJSDate(),
    range_to.value.toJSDate(),
  );

  let expenses_paid = filterPaid(expenses);
  sum.value = sumItemProp(expenses_paid, "price");
});
</script>

<style scoped></style>
