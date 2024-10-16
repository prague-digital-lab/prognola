<template>
  <div>
    <div v-if="loaded">
      <page-content-header>
        <template v-slot:title>
          <h2
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            Výhled hospodaření
          </h2>
        </template>
      </page-content-header>

      <p class="">Aktuální zůstatek napříč účty: 10 000 Kč</p>
      <p class="mb-10">Hotovost na pokladnách: 17 000 Kč</p>
      <p></p>

      <div
        class="mb-4 rounded-md border border-red-200 bg-red-50 p-4"
        v-if="incomes_due.length > 0 || expenses_due.length > 0"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <ExclamationCircleIcon
              class="h-5 w-5 text-red-400"
              aria-hidden="true"
            />
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
              Tyto příjmy a výdaje nebyly uhrazené v naplánovaném termínu.
            </h3>
            <div class="mt-2 text-sm text-red-700">
              Můžete je odložit do budoucna, podle toho, kdy očekáváte jejich
              uhrazení.
            </div>
          </div>
        </div>
      </div>

      <div class="mb-4" v-if="incomes_due.length > 0">
        <p class="mb-2">Zpožděné příjmy</p>
        <income-row :income="income" v-for="income in incomes_due"></income-row>
      </div>

      <div class="mb-4" v-if="expenses_due.length > 0">
        <p class="mb-2">Zpožděné výdaje</p>
        <expense-row
          :expense="expense"
          v-for="expense in expenses_due"
        ></expense-row>
      </div>

      <div class="mb-4">
        <p class="mb-2">Dnešní příjmy</p>
        <income-row
          :income="income"
          v-for="income in incomes_today"
        ></income-row>

        <div v-if="incomes_today.length === 0">-</div>
      </div>

      <div class="mb-4">
        <p class="mb-2 mt-2">Dnešní výdaje</p>
        <expense-row
          :expense="expense"
          v-for="expense in expenses_today"
        ></expense-row>

        <div v-if="expenses_today.length === 0">-</div>
      </div>
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
import { DateTime } from "luxon";
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import { ExclamationCircleIcon } from "@heroicons/vue/20/solid";

useHead({
  title: "Výhled - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const loaded = ref(true);

onMounted(() => {
  fetchData();
});

const client = useSanctumClient();
const route = useRoute();

const yesterday_end = DateTime.now().minus({ days: 1 }).endOf("day").toISO();

const { data: response } = await useAsyncData("expenses_due", () =>
  client("/api/" + route.params.workspace + "/expenses", {
    method: "GET",
    params: {
      to: yesterday_end,
      payment_status: "pending",
    },
  }),
);
const expenses_due = response.value.data;

const { data: response_2 } = await useAsyncData("incomes_due", () =>
  client("/api/" + route.params.workspace + "/incomes", {
    method: "GET",
    params: {
      to: yesterday_end,
      payment_status: "pending",
    },
  }),
);

const incomes_due = response_2.value.data;

// Today
const today_start = DateTime.now().startOf("day").toISO();
const today_end = DateTime.now().endOf("day").toISO();

const { data: response_3 } = await useAsyncData("expenses_today", () =>
  client("/api/" + route.params.workspace + "/expenses", {
    method: "GET",
    params: {
      from: today_start,
      to: today_end,
    },
  }),
);
const expenses_today = response_3.value.data;

const { data: response_4 } = await useAsyncData("incomes_today", () =>
  client("/api/" + route.params.workspace + "/incomes", {
    method: "GET",
    params: {
      from: today_start,
      to: today_end,
    },
  }),
);

const incomes_today = response_4.value.data;

async function fetchData() {
  // console.log(expenses_due.data.value.data)
  // expenses_due.value = expenses_due.data.value.data;
  // incomes_due.value = incomes_due;
}
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
