<template>
  <div
    class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900"
  >
    <nuxt-link href="expenses">
      <div
        class="mb-3 flex items-center justify-between border-b border-gray-200 dark:border-zinc-800 px-4 pb-2 pt-3"
      >
        <div class="truncate text-gray-600 dark:text-zinc-400">
          Výdaje <span class="text-gray-400">- tento měsíc</span>
        </div>
        <div class="text-xl font-semibold tracking-tight text-red-600">
          {{ formatPrice(sum) }} Kč
        </div>
      </div>
    </nuxt-link>

    <div class="px-4 py-1">

    <p class="mb-2 text-sm text-zinc-500">Poslední uhrazené</p>

    <div
      class="mb-4 divide-y divide-gray-200 rounded border  border-gray-200 bg-white dark:divide-zinc-800 dark:border-zinc-800"
    >
      <nuxt-link
        v-for="expense in paid_expenses"
        :href="'/' + route.params.workspace + '/expenses/' + expense.uuid"
        class="block"
        :key="expense.uuid"
      >
        <expense-row-mobile
          class="bg-zinc-50"
          :expense="expense"
          :key="expense.uuid"
          :organisation="expense.organisation"
          :force_mobile="true"
        ></expense-row-mobile>
      </nuxt-link>
    </div>
  </div>
  </div>
</template>

<script setup>
import { sumItemProp } from "~/lib/dexie/data_helpers.js";
import { DateTime } from "luxon";
import { getExpensesByPaidAt } from "~/lib/dexie/repository/expense_repository.js";
import ExpenseRowMobile from "~/components/expense/expense_row/ExpenseRowMobile.vue";
import { findOrganisation } from "~/lib/dexie/repository/organisation_repository.js";

const route = useRoute();

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
const paid_expenses = ref([]);

onMounted(async () => {
  range_from.value = DateTime.now().startOf("month");
  range_to.value = DateTime.now().endOf("month");

  let expenses = await getExpensesByPaidAt(
    range_from.value.toJSDate(),
    range_to.value.toJSDate(),
  );

  paid_expenses.value = filterPaid(expenses);

  paid_expenses.value.map(async (expense) => {
    if (expense.organisation) {
      expense.organisation = await findOrganisation(expense.organisation);
    }
  });

  sum.value = sumItemProp(paid_expenses.value, "price");
});
</script>

<style scoped></style>
