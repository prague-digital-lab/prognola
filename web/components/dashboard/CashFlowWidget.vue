<template>
  <div v-if="loaded">
  <page-content-header>
    <template v-slot:title>
      <h2
          class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight dark:text-zinc-200"
      >
        Cash flow
      </h2>
    </template>

    <template v-slot:controls>
      <div class="me-2">
        <input
            type="date"
            v-model="from_picker"
            class="w-full rounded-md border border-gray-200 py-1.5 text-base text-gray-700 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:block sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
        />
      </div>

      <div>
        <input
            type="date"
            v-model="to_picker"
            class="w-full rounded-md border border-gray-200 py-1.5 text-base text-gray-700 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:block sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
        />
      </div>
    </template>
  </page-content-header>

  <div class="mb-7">
    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
      <div
          class="overflow-hidden rounded-lg border border-gray-200 bg-white px-3 py-4 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900"
      >
        <nuxt-link href="income">
          <dt class="truncate text-gray-500 dark:text-zinc-400">Příjmy</dt>
        </nuxt-link>
        <dd
            class="mt-1 text-xl font-semibold tracking-tight text-indigo-600"
        >
          {{ formatPrice(income_sum) }} Kč
          <span
              class="ms-1 mt-1 text-xl font-semibold tracking-tight text-indigo-400"
          >
                + {{ formatPrice(income_plan_sum) }} Kč</span
          >
        </dd>
      </div>
      <div
          class="overflow-hidden rounded-lg border border-gray-200 bg-white px-3 py-4 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900"
      >
        <nuxt-link href="expenses">
          <dt class="truncate text-gray-500 dark:text-zinc-400">Výdaje</dt>
        </nuxt-link>
        <dd class="mt-1 text-xl font-semibold tracking-tight text-red-600">
          {{ formatPrice(expense_sum) }} Kč
          <span
              class="ms-1 mt-1 text-xl font-semibold tracking-tight text-red-400"
          >
                + {{ formatPrice(expense_plan_sum) }} Kč</span
          >
        </dd>
      </div>
      <div
          class="overflow-hidden rounded-lg border border-gray-200 bg-white px-3 py-4 sm:p-5 dark:border-zinc-800 dark:bg-zinc-900"
      >
        <dt class="truncate text-gray-500 dark:text-zinc-400">Výsledek</dt>
        <dd
            class="mt-1 text-xl font-semibold tracking-tight text-gray-900 dark:text-zinc-200"
        >
          {{ formatPrice(profit_sum + profit_plan_sum) }} Kč
        </dd>
      </div>
    </dl>
  </div>

  <Bar
      id="my-chart-id"
      height="55%"
      :options="chartOptions"
      :data="chartData"
  />
  </div>
  <div v-else class="flex h-[600px] items-center justify-center">
    <div class="atom-spinner">
      <div class="spinner-inner">
        <div class="spinner-line"></div>
        <div class="spinner-line"></div>
        <div class="spinner-line"></div>
        <!--Chrome renders little circles malformed :(-->
        <div class="spinner-circle">&#9679;</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import { Bar } from "vue-chartjs";
import {
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  LineController,
  LineElement,
  PointElement,
  Title,
  Tooltip,
} from "chart.js";
import { DateTime } from "luxon";
import colors from "tailwindcss/colors";
import { getExpensesByPaidAt } from "~/lib/dexie/repository/expense_repository.js";
import { getIncomesByPaidAt } from "~/lib/dexie/repository/income_repository.js";
import {
  countBalancePrognosisFromNow,
  getBalancePrognosisByDate,
} from "~/lib/dexie/repository/balance_prognosis_repository.js";


ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    BarElement,
    LineController,
    LineElement,
    Title,
    Tooltip,
    Legend,
);

const loaded = ref(false);

const from = ref();
const to = ref();

const from_picker = ref();
const to_picker = ref();

const chartData = ref({});
const chartOptions = ref({
  onClick: (event, elements, chart) => {
    if (elements[0]) {
      const item_index = elements[0].index;
      const dataset_index = elements[0].datasetIndex;

      const date_from = from.value
          .plus({ months: item_index })
          .startOf("month");
      const date_to = date_from.endOf("month");

      localStorage.setItem("from", date_from.toFormat("yyyy-MM-dd"));
      localStorage.setItem("to", date_to.toFormat("yyyy-MM-dd"));

      if (dataset_index === 0 || dataset_index === 1) {
        navigateToIncomes();
      }
      if (dataset_index === 2 || dataset_index === 3) {
        navigateToExpenses();
      }
    }
  },

  plugins: {
    legend: {
      display: false,
      position: "bottom",
    },
  },

  responsive: true,
  scales: {
    y: {
      grid: {
        display: false,
      },
      beginAtZero: true,
      type: "linear",
      position: "right",
    },
    x: {
      grid: {
        display: false,
      },
    },
  },
});

const income_sum = ref(0);
const income_plan_sum = ref(0);
const expense_sum = ref(0);
const expense_plan_sum = ref(0);
const profit_sum = ref(0);
const profit_plan_sum = ref(0);

onMounted(() => {
  from.value =  DateTime.now().startOf("year")
  to.value =  DateTime.now().endOf("year").endOf("day")

  from_picker.value = from.value.toFormat("yyyy-MM-dd");
  to_picker.value = to.value.toFormat("yyyy-MM-dd");

  fetchData();
});

async function fetchData() {
  await countBalancePrognosisFromNow();

  let range_start = from.value;
  let range_end = from.value.endOf("month");

  let expenses_planned_data = [];
  let expenses_issued_data = [];
  let incomes_planned_data = [];
  let incomes_issued_data = [];
  let labels_data = [];
  let balance_data = [];

  let income_sum_stats = 0;
  let expense_sum_stats = 0;
  let income_plan_sum_stats = 0;
  let expense_plan_sum_stats = 0;

  while (range_end <= to.value) {
    let expenses = await getExpensesByPaidAt(
        range_start.toJSDate(),
        range_end.toJSDate(),
    );
    let incomes = await getIncomesByPaidAt(
        range_start.toJSDate(),
        range_end.toJSDate(),
    );
    let range_end_balance = await getBalancePrognosisByDate(
        range_end.startOf("day").toJSDate(),
    );

    let expenses_issued = filterPaid(expenses);
    let expenses_plan = filterPlanned(expenses);
    let incomes_issued = filterPaid(incomes);
    let incomes_plan = filterPlanned(incomes);

    let expenses_issued_range_sum = sumItemProp(expenses_issued, "price");
    let expenses_plan_range_sum = sumItemProp(expenses_plan, "price");
    let income_plan_range_sum = sumItemProp(incomes_plan, "amount");
    let income_issued_range_sum = sumItemProp(incomes_issued, "amount");

    expenses_issued_data.push(expenses_issued_range_sum);
    expenses_planned_data.push(expenses_plan_range_sum);
    incomes_planned_data.push(income_plan_range_sum);
    incomes_issued_data.push(income_issued_range_sum);

    if (range_end_balance) {
      balance_data.push(range_end_balance.balance);
    } else {
      balance_data.push(0);
    }

    labels_data.push(range_start.toFormat("MM/yyyy"));

    // Sums
    income_sum_stats = income_sum_stats + income_issued_range_sum;
    expense_sum_stats = expense_sum_stats + expenses_issued_range_sum;
    income_plan_sum_stats = income_plan_sum_stats + income_plan_range_sum;
    expense_plan_sum_stats = expense_plan_sum_stats + expenses_plan_range_sum;

    range_start = range_start.plus({ month: 1 });
    range_end = range_start.endOf("month");
  }

  income_sum.value = income_sum_stats;
  expense_sum.value = expense_sum_stats;
  income_plan_sum.value = income_plan_sum_stats;
  expense_plan_sum.value = expense_plan_sum_stats;

  profit_sum.value = income_sum_stats - expense_sum_stats;
  profit_plan_sum.value = income_plan_sum_stats - expense_plan_sum_stats;

  chartData.value = {
    labels: labels_data,
    datasets: [
      {
        label: "Příjmy",
        data: incomes_issued_data,
        backgroundColor: [colors.indigo[400]],
        hidden: false,
        cubicInterpolationMode: "monotone",
        tension: 0.1,
        stack: "stack 0",
        borderRadius: 4,
      },

      {
        label: "Plán příjmů",
        data: incomes_planned_data,
        backgroundColor: [colors.indigo[200]],
        hidden: false,
        cubicInterpolationMode: "monotone",
        tension: 0.1,
        stack: "stack 0",
        borderRadius: 4,
      },

      {
        label: "Výdaje",
        data: expenses_issued_data,
        hidden: false,
        backgroundColor: [colors.red[400]],
        cubicInterpolationMode: "monotone",
        tension: 0.1,
        stack: "stack 1",
        borderRadius: 4,
      },

      {
        label: "Plán výdajů",
        data: expenses_planned_data,
        backgroundColor: [colors.red[200]],
        hidden: false,
        cubicInterpolationMode: "monotone",
        tension: 0.1,
        stack: "stack 1",
        borderRadius: 4,
      },

      {
        label: "Konečný zůstatek",
        data: balance_data,
        borderColor: [colors.indigo[200]],
        hidden: false,
        borderWidth: 2,
        cubicInterpolationMode: "monotone",
        tension: 0.1,
        type: "line",
      },
    ],
  };

  loaded.value = true;
}



function filterPaid(items) {
  return items.filter((item) => {
    return item.payment_status === "paid";
  });
}

function filterPlanned(items) {
  return items.filter((item) => {
    return item.payment_status === "plan" || item.payment_status === "pending";
  });
}

function sumItemProp(items, prop_name) {
  return items.reduce(function (a, b) {
    return a + b[prop_name];
  }, 0);
}

async function navigateToExpenses() {
  const route = useRoute();
  await navigateTo("/" + route.params.workspace + "/expenses");
}

async function navigateToIncomes() {
  const route = useRoute();
  await navigateTo("/" + route.params.workspace + "/income");
}

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

watch(from_picker, (newValue, oldValue) => {
  if (oldValue === null) {
    return;
  }

  from.value = DateTime.fromFormat(newValue, "yyyy-MM-dd");

  fetchData();
});

watch(to_picker, (newValue, oldValue) => {
  if (oldValue === null) {
    return;
  }

  to.value = DateTime.fromFormat(newValue, "yyyy-MM-dd").endOf("day");

  fetchData();
});
</script>

<style>
.atom-spinner,
.atom-spinner * {
  box-sizing: border-box;
}

.atom-spinner {
  height: 100px;
  width: 100px;
  overflow: hidden;
}

.atom-spinner .spinner-inner {
  position: relative;
  display: block;
  height: 100%;
  width: 100%;
}

.atom-spinner .spinner-circle {
  display: block;
  position: absolute;
  color: rgb(79 70 229);
  font-size: calc(60px * 0.24);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.atom-spinner .spinner-line {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  animation-duration: 1s;
  border-left-width: calc(60px / 25);
  border-top-width: calc(60px / 25);
  border-left-color: rgb(79 70 229);
  border-left-style: solid;
  border-top-style: solid;
  border-top-color: transparent;
}

.atom-spinner .spinner-line:nth-child(1) {
  animation: atom-spinner-animation-1 2s linear infinite;
  transform: rotateZ(120deg) rotateX(66deg) rotateZ(0deg);
}

.atom-spinner .spinner-line:nth-child(2) {
  animation: atom-spinner-animation-2 2s linear infinite;
  transform: rotateZ(240deg) rotateX(66deg) rotateZ(0deg);
}

.atom-spinner .spinner-line:nth-child(3) {
  animation: atom-spinner-animation-3 2s linear infinite;
  transform: rotateZ(360deg) rotateX(66deg) rotateZ(0deg);
}

@keyframes atom-spinner-animation-1 {
  100% {
    transform: rotateZ(120deg) rotateX(66deg) rotateZ(360deg);
  }
}

@keyframes atom-spinner-animation-2 {
  100% {
    transform: rotateZ(240deg) rotateX(66deg) rotateZ(360deg);
  }
}

@keyframes atom-spinner-animation-3 {
  100% {
    transform: rotateZ(360deg) rotateX(66deg) rotateZ(360deg);
  }
}
</style>
