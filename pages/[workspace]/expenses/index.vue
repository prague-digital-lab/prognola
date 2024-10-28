<template>
  <div>
    <div v-if="loaded">
      <page-content-header>
        <template v-slot:title>
          <heading>Výdaje</heading>
        </template>
        <template v-slot:subtitle>
          <p class="text-base text-gray-500 dark:text-zinc-400">
            Celkem: {{ formatPrice(price_sum) }} Kč
          </p>
        </template>

        <template v-slot:controls>
          <div class="mt-4 flex md:ml-4 md:mt-0">
            <div class="me-2">
              <button-secondary class="h-full" @click="downloadExport"
                >Export
              </button-secondary>
            </div>

            <div class="me-2">
              <select
                v-model="grouped_by"
                class="block w-full rounded-md border border-gray-200 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
              >
                <option :value="null">Seskupit</option>
                <option value="expense_category">Kategorie</option>
              </select>
            </div>

            <div class="me-2">
              <input
                type="date"
                v-model="from"
                class="block w-full rounded-md border border-gray-200 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
              />
            </div>

            <div>
              <input
                type="date"
                v-model="to"
                class="block w-full rounded-md border border-gray-200 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
              />
            </div>
          </div>
        </template>
      </page-content-header>

      <div
        v-if="expenses.length === 0"
        class="mb-4 flex h-[400px] w-full items-center justify-center rounded-md border border-gray-200 dark:border-zinc-800 dark:text-zinc-400"
      >
        <p class="text-gray-600 dark:text-zinc-400">
          Žádné odpovídající výdaje.
        </p>
      </div>

      <div v-if="expenses.length > 0">
        <div
          class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 bg-white dark:divide-zinc-800 dark:border-zinc-800"
          v-if="grouped_by === null"
        >
          <expense-row
            v-for="expense in expenses"
            :expense="expense"
          ></expense-row>
        </div>

        <div
          v-if="grouped_by === 'expense_category'"
          class="mb-4 rounded border border-gray-200 dark:border-zinc-800"
        >
          <div
            class="divide-y divide-gray-200 dark:divide-zinc-800"
            v-for="category in expenses_by_category"
          >
            <div
              class="w-full bg-slate-100 py-1 ps-3 text-base text-gray-600 dark:bg-zinc-700 dark:text-zinc-200"
            >
              {{
                category[0].expense_category
                  ? category[0].expense_category.name
                  : "Výdaje bez kategorie"
              }}
            </div>
            <expense-row
              v-for="expense in category"
              :expense="expense"
            ></expense-row>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button-secondary @click="openModal">+ nový výdaj</button-secondary>
      </div>

      <expense-create-modal
        ref="modal_create"
        :default_paid_at="from"
        @expense-created="fetchExpenses"
      />
    </div>
  </div>
</template>

<script setup>
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";
import ExpenseCreateModal from "~/components/ui/modals/expense_create_modal/ExpenseCreateModal.vue";
import Heading from "~/components/ui/Heading.vue";
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import { DateTime } from "luxon";
import { getExpensesByPaidAt } from "~/lib/dexie/repository/expense_repository.js";

useHead({
  title: "Výdaje - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const modal_create = useTemplateRef("modal_create");

function openModal() {
  modal_create.value.openModal();
}

const from = ref("2024-07-01");
const to = ref("2024-07-30");

const grouped_by = ref(null);
const filter_category_id = ref(null);
const filter_organisation_id = ref(null);

onMounted(() => {
  if (localStorage.getItem("from")) {
    from.value = localStorage.getItem("from");
  } else {
    from.value = "2024-07-01";
  }

  if (localStorage.getItem("to")) {
    to.value = localStorage.getItem("to");
  } else {
    to.value = "2024-07-30";
  }

  if (localStorage.getItem("expenses.grouped_by")) {
    grouped_by.value = localStorage.getItem("expenses.grouped_by");
  } else {
    grouped_by.value = null;
  }

  fetchExpenses();
});

const date_start = ref();

// const date_start = computed(() => {
//   return DateTime.fromISO(from.value).toJSDate();
// });

const date_end = computed(() => {
  return DateTime.fromISO(to.value).toJSDate();
});

const query = ref("");

// const expenses = useObservable(
//   liveQuery(() => {
//     return db.expenses
//       .where("paid_at")
//       .between(date_start.value, date_end.value)
//       .toArray();
//   }),
// );

const expenses = ref();

async function fetchExpenses() {
  let date_from = DateTime.fromISO(from.value).toJSDate();
  let date_to = DateTime.fromISO(to.value).toJSDate();

  expenses.value = await getExpensesByPaidAt(date_from, date_to);
}

const price_sum = computed(() => {
  return expenses.value.reduce(function (a, b) {
    return a + b.price;
  }, 0);
});

const expenses_by_category = computed(() => {
  return this.expenses.reduce(function (r, expense) {
    r[expense.expense_category_id] = r[expense.expense_category_id] || [];
    r[expense.expense_category_id].push(expense);
    return r;
  }, Object.create(null));
});

const loaded = computed(() => {
  return expenses.value;
});

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

async function downloadExport() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("export", () =>
    client("/api/" + route.params.workspace + "/download/expenses/scans/url", {
      method: "GET",
      query: {
        from: this.from,
        to: this.to,
      },
    }),
  );

  window.open(data.value.export_url, "_blank");
}

watch(from, (newVal, oldVal) => {
  localStorage.setItem("from", newVal);
  fetchExpenses();
});

watch(to, (newVal, oldVal) => {
  localStorage.setItem("to", newVal);
  fetchExpenses();
});

watch(grouped_by, (newVal, oldVal) => {
  localStorage.setItem("expenses.grouped_by", newVal);

  if (newVal === null) {
    localStorage.removeItem("expenses.grouped_by");
  }
});
</script>
