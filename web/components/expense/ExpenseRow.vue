<template>
  <nuxt-link
    :href="'/' + route.params.workspace + '/expenses/' + expense.uuid"
    class="block"
    :key="expense.uuid"
  >
    <div
      class="hidden md:flex items-center justify-between bg-white px-3 py-2 hover:bg-gray-hover dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
    >
      <div class="flex text-base">
        <p class="md:w-[400px]">
          {{ expense.description ? expense.description : "nový výdaj" }}
        </p>
      </div>

      <div class="flex items-center text-base font-light text-slate-600">
        <div
          v-if="organisation"
          class="me-2 flex cursor-pointer items-center rounded-md border border-gray-200 px-3 py-1 leading-5 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400"
          @click="navigateToOrganisation(organisation)"
        >
          <building-library-icon
            :key="expense.uuid"
            class="me-1 h-4 w-4"
          ></building-library-icon>

          {{ organisation.name }}
        </div>

        <p
          class="me-2 w-[90px] ps-4 dark:text-zinc-400"
          v-if="expense.received_at"
        >
          {{ formatDate(expense.received_at) }}
        </p>

        <context-menu-paid-at-picker
          :key="expense.uuid"
          @expense-updated="$emit('expense-updated')"
          :expense="expense"
        ></context-menu-paid-at-picker>

        <div class="w-[120px]">
          <expense-status-badge :expense="expense"></expense-status-badge>
        </div>

        <div class="ms-2">
          <expense-status-icon :expense="expense" :key="expense.uuid" />
        </div>
      </div>
    </div>

    <div class="block md:hidden p-3">
      <div class="flex mb-2 justify-between items-center">
        <p class="md:w-[400px]">
          {{ expense.description ? expense.description : "nový výdaj" }}
        </p>

        <div class="flex items-center">
          <div class="w-[120px]">
            <expense-status-badge :expense="expense"></expense-status-badge>
          </div>

          <div class="ms-2">
            <expense-status-icon :expense="expense" :key="expense.uuid" />
          </div>
        </div>
      </div>

      <div class="flex justify-between">
        <div
          v-if="organisation"
          class="me-2 flex cursor-pointer items-center rounded-md border border-gray-200 px-2 py-[1px] leading-5  text-[12px] dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400 text-zinc-500"
          @click="navigateToOrganisation(organisation)"
        >
          <building-library-icon
            :key="expense.uuid"
            class="me-1 h-4 w-4"
          ></building-library-icon>

          {{ organisation.name }}
        </div>
        <div v-else></div>

        <div class="flex justify-between items-center">
          <context-menu-paid-at-picker
            :key="expense.uuid"
            @expense-updated="$emit('expense-updated')"
            :expense="expense"
          ></context-menu-paid-at-picker>
        </div>
      </div>

    </div>
  </nuxt-link>
</template>

<script setup>
import { DateTime } from "luxon";
import { BuildingLibraryIcon } from "@heroicons/vue/24/outline/index.js";
import { findOrganisation } from "~/lib/dexie/repository/organisation_repository.js";
import ContextMenuPaidAtPicker from "~/components/expense/expense_row/ContextMenuPaidAtPicker.vue";
import ExpenseStatusBadge from "~/components/expense/expense_row/ExpenseStatusBadge.vue";

const props = defineProps(["expense"]);
const route = useRoute();

const organisation = ref(null);

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function formatDate(date) {
  let formatted = DateTime.fromJSDate(date);

  return formatted.toFormat("d.M.yyyy");
}

async function navigateToExpense() {
  const route = useRoute();
  await navigateTo(
    "/" + route.params.workspace + "/expenses/" + props.expense.uuid
  );
}

async function navigateToOrganisation(organisation) {
  const route = useRoute();
  await navigateTo(
    "/" + route.params.workspace + "/organisations/" + organisation.uuid
  );
}

onMounted(async () => {
  if (props.expense.organisation) {
    organisation.value = await findOrganisation(props.expense.organisation);
  }
});
</script>

<style scoped></style>
