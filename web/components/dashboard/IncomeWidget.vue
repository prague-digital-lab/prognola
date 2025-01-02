<template>
  <div
    class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900"
  >
    <nuxt-link href="income">
      <div
        class="mb-3 flex items-center justify-between border-b border-gray-200 px-4 pb-2 pt-3 dark:border-zinc-800"
      >
        <div class="truncate text-gray-600 dark:text-zinc-300">
          Příjmy <span class="text-gray-400">- tento měsíc</span>
        </div>
        <div
          class="text-xl font-semibold tracking-tight text-green-700 dark:text-green-500"
        >
          {{ formatPrice(sum) }} Kč
        </div>
      </div>
    </nuxt-link>

    <div class="px-4 py-1">
      <p class="mb-2 text-sm text-zinc-500">Poslední uhrazené</p>

      <div
        class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 bg-white dark:divide-zinc-800 dark:border-zinc-800"
      >
        <nuxt-link
          v-for="income in latest_incomes"
          :href="'/' + route.params.workspace + '/income/' + income.uuid"
          class="block"
          :key="income.uuid"
        >
          <income-row-mobile
            class="bg-zinc-50"
            :income="income"
            :key="income.uuid"
            :organisation="income.organisation"
            :force_mobile="true"
          ></income-row-mobile>
        </nuxt-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { sumItemProp } from "~/lib/dexie/data_helpers.js";
import { DateTime } from "luxon";
import { getIncomesByPaidAt } from "~/lib/dexie/repository/income_repository.js";
import IncomeRowMobile from "~/components/income/income_row/IncomeRowMobile.vue";

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function filterPaid(items) {
  return items.filter((item) => {
    return item.payment_status === "paid";
  });
}

function limitItems(items, count){
  return items.slice(0, count);
}

const sum = ref(0);
const range_from = ref();
const range_to = ref();
const latest_incomes = ref([]);

const route = useRoute()

onMounted(async () => {
  range_from.value = DateTime.now().startOf("month");
  range_to.value = DateTime.now().endOf("month");

  let incomes = await getIncomesByPaidAt(
    range_from.value.toJSDate(),
    range_to.value.toJSDate(),
  );

  let incomes_paid = filterPaid(incomes).reverse()

  sum.value = sumItemProp(incomes_paid, "amount");
  latest_incomes.value = limitItems(incomes_paid, 3);
});
</script>

<style scoped></style>
