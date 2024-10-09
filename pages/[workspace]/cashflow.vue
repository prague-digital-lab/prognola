<template>
  <div>
    <div v-if="loaded">
      <page-content-header>
        <template v-slot:title>
          <h2
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            Cashflow
          </h2>
        </template>

        <template v-slot:controls>
          <div class="me-2">
            <input
              type="date"
              v-model="from"
              class="w-full rounded-md border border-gray-200 py-1.5 text-base text-gray-700 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:block sm:leading-6"
            />
          </div>

          <div>
            <input
              type="date"
              v-model="to"
              class="w-full rounded-md border border-gray-200 py-1.5 text-base text-gray-700 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:block sm:leading-6"
            />
          </div>
        </template>
      </page-content-header>

      <div class="mb-5">
        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
          <div
            class="overflow-hidden rounded-lg border border-gray-200 bg-white px-4 py-5 sm:p-6"
          >
            <nuxt-link href="income">
              <dt class="truncate text-gray-500">Příjmy</dt>
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
            class="overflow-hidden rounded-lg border border-gray-200 bg-white px-4 py-5 sm:p-6"
          >
            <nuxt-link href="expenses">
              <dt class="truncate text-gray-500">Výdaje</dt>
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
            class="overflow-hidden rounded-lg border border-gray-200 bg-white px-4 py-5 sm:p-6"
          >
            <dt class="truncate text-gray-500">Výsledek</dt>
            <dd class="mt-1 text-xl font-semibold tracking-tight text-gray-900">
              {{ formatPrice(profit_sum + profit_plan_sum) }} Kč
            </dd>
          </div>
        </dl>
      </div>

      <Bar
        id="my-chart-id"
        height="150%"
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
  </div>
</template>

<script setup>
useHead({
  title: "Cashflow - Prognola",
});

import PageContentHeader from "~/components/ui/PageContentHeader.vue";
</script>

<script>
import {
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  Title,
  Tooltip,
} from "chart.js";
import { Bar } from "vue-chartjs";
import colors from "tailwindcss/colors";

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  BarElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
);

export default {
  setup() {
    definePageMeta({
      layout: "default",
      middleware: ["sanctum:auth", "sanctum:verified"],
    });
  },

  components: { Bar },

  data() {
    return {
      loaded: false,

      from: "2024-01-01",
      to: "2024-12-31",

      chartData: {},
      chartOptions: {
        plugins: {
          legend: {
            display: false,
            position: 'bottom'
          }
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
      },

      income_sum: 0,
      income_plan_sum: 0,
      expense_sum: 0,
      expense_plan_sum: 0,
      profit_sum: 0,
      profit_plan_sum: 0,
    };
  },

  mounted() {
    this.fetchData();
  },

  watch: {
    from: function (newVal, oldVal) {
      this.fetchData();
    },
    to: function (newVal, oldVal) {
      this.fetchData();
    },
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client("/api/" + route.params.workspace + "/stats/cashflow", {
          method: "GET",
          params: {
            from: this.from,
            to: this.to,
          },
        }),
      );

      this.chartData = {
        labels: data.value.chart_labels,
        datasets: [
          {
            label: "Příjmy",
            data: data.value.chart_data_income,
            backgroundColor: [colors.indigo[400]],
            hidden: false,
            cubicInterpolationMode: "monotone",
            tension: 0.1,
            stack: "stack 0",
            borderRadius: 4,
          },

          {
            label: "Plán příjmů",
            data: data.value.chart_data_income_plan,
            backgroundColor: [colors.indigo[200]],
            hidden: false,
            cubicInterpolationMode: "monotone",
            tension: 0.1,
            stack: "stack 0",
            borderRadius: 4,
          },

          {
            label: "Výdaje",
            data: data.value.chart_data_expense,
            hidden: false,
            backgroundColor: [colors.red[400]],
            cubicInterpolationMode: "monotone",
            tension: 0.1,
            stack: "stack 1",
            borderRadius: 4,
          },

          {
            label: "Plán výdajů",
            data: data.value.chart_data_expense_plan,
            backgroundColor: [colors.red[200]],
            hidden: false,
            cubicInterpolationMode: "monotone",
            tension: 0.1,
            stack: "stack 1",
            borderRadius: 4,
          },

          {
            label: "Konečný zůstatek",
            data: data.value.chart_data_balance,
            backgroundColor: ["rgba(0,0,0,0.56)"],
            hidden: false,
            cubicInterpolationMode: "monotone",
            tension: 0.1,
            type: "line",
          },
        ],
      };

      this.income_sum = data.value.income_sum;
      this.income_plan_sum = data.value.income_plan_sum;
      this.expense_sum = data.value.expense_sum;
      this.expense_plan_sum = data.value.expense_plan_sum;
      this.profit_sum = data.value.profit_sum;
      this.profit_plan_sum = data.value.profit_plan_sum;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },
  },
};
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
