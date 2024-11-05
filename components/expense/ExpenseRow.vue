<template>
  <nuxt-link :href="'/' + route.params.workspace + '/expenses/' + expense.uuid" class="block" :key="expense.uuid">
  <div
    class="flex items-center justify-between bg-white px-3 py-2 hover:bg-gray-hover dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
  >
    <div class="flex text-base">
      <!--      <p class="w-[60px] font-light text-gray-500">V-{{ expense.id }}</p>-->
      <p class="w-[400px]">
        {{ expense.description ? expense.description : "nový výdaj" }}
      </p>
    </div>

    <div class="flex items-center text-base font-light text-slate-600">
      <div
        v-if="organisation"
        class="me-2 flex cursor-pointer items-center rounded-md border border-gray-200 px-3 py-1 leading-5 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400"
        @click="navigateToOrganisation(organisation)"
      >
        <building-library-icon class="me-1 h-4 w-4"></building-library-icon>

        {{ organisation.name }}
      </div>

      <p
        class="me-2 w-[90px] ps-4 dark:text-zinc-400"
        v-if="expense.received_at"
      >
        {{ formatDate(expense.received_at) }}
      </p>

      <context-menu-paid-at-picker @expense-updated="$emit('expense-updated')" :expense="expense"></context-menu-paid-at-picker>


      <div class="w-[120px]">
        <div v-if="expense.payment_status === 'paid'">
          <p
            class="ms-2 text-end font-semibold text-slate-700 dark:text-zinc-300"
          >
            {{ formatPrice(expense.price) }} Kč
          </p>
        </div>

        <div v-else-if="expense.payment_status === 'draft'">
          <p
            class="me-2 text-end font-semibold text-purple-800 dark:text-indigo-500"
          >
            ke zpracování
          </p>
        </div>

        <div v-else-if="expense.payment_status === 'plan'">
          <p class="ms-2 text-end font-semibold text-yellow-500">
            {{ formatPrice(expense.price) }} Kč
          </p>
        </div>

        <div v-else>
          <p class="ms-2 text-end font-bold text-red-700" v-if="isDue">
            {{ formatPrice(expense.price) }} Kč
          </p>

          <p class="ms-2 text-end font-bold text-orange-400" v-else>
            {{ formatPrice(expense.price) }} Kč
          </p>
        </div>
      </div>

      <div class="ms-2">
        <expense-status-icon :expense="expense" />
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

const props = defineProps(["expense"]);
const route = useRoute()

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
    "/" + route.params.workspace + "/expenses/" + props.expense.uuid,
  );
}

async function navigateToOrganisation(organisation) {
  const route = useRoute();
  await navigateTo(
    "/" + route.params.workspace + "/organisations/" + organisation.uuid,
  );
}

onMounted(async () => {
  if (props.expense.organisation) {
    organisation.value = await findOrganisation(props.expense.organisation);
  }
});

const isDue = computed(() => {
  const today = DateTime.now().startOf("day");

  const paid_at = DateTime.fromJSDate(props.expense.paid_at);
  return paid_at < today;
});

</script>

<style scoped></style>
