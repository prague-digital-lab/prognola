<template>
  <div>
    <div>
      <page-content-header>
        <template v-slot:title>
          <heading>Nadcházející platby</heading>
        </template>
      </page-content-header>

      <p class="dark:text-zinc-400">Aktuální zůstatek napříč účty: 0 Kč</p>
      <p class="mb-10 dark:text-zinc-400">Hotovost na pokladnách: 0 Kč</p>

      <div
        class="mb-4 flex flex-col space-y-3 rounded-md border border-red-300 p-5 dark:border-zinc-800 dark:bg-zinc-950"
        v-if="incomes_due.length > 0 || expenses_due.length > 0"
      >
        <div
          class="space mb-4 rounded-md border border-red-200 bg-red-50 p-4 dark:border-zinc-800 dark:bg-zinc-900"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <ExclamationCircleIcon
                class="h-5 w-5 text-red-400 dark:text-red-900"
                aria-hidden="true"
              />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800 dark:text-white">
                <span v-if="incomes_due.length > 0 && expenses_due.length > 0"
                  >Tyto příjmy a výdaje nebyly uhrazené v naplánovaném
                  termínu.</span
                >
                <span v-if="incomes_due.length > 0 && expenses_due.length === 0"
                  >Tyto příjmy nebyly uhrazené v naplánovaném termínu.</span
                >
                <span v-if="incomes_due.length === 0 && expenses_due.length > 0"
                  >Tyto výdaje nebyly uhrazené v naplánovaném termínu.</span
                >
              </h3>
              <div class="mt-2 text-sm text-red-700 dark:text-zinc-400">
                Můžete je odložit do budoucna, podle toho, kdy očekáváte jejich
                uhrazení.
              </div>
            </div>
          </div>
        </div>

        <div v-if="incomes_due.length > 0">
          <div class="mb-4 flex justify-between">
            <div class="dark:text-zinc-400">Zpožděné příjmy</div>
            <div>
              <span
                class="rounded-md bg-blue-100 px-3 py-1 font-bold text-blue-700 dark:border dark:border-blue-400/30 dark:bg-blue-400/10 dark:text-blue-400"
                >{{ formatPrice(incomes_due_sum) }} Kč</span
              >
            </div>
          </div>
          <div
            class=" mb-4 divide-y divide-gray-200 border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
          >
            <income-row
              :income="income"
              v-for="income in incomes_due"
            ></income-row>
          </div>
        </div>

        <div v-if="expenses_due.length > 0">
          <div class="mb-4 flex justify-between">
            <div class="dark:text-zinc-400">Zpožděné výdaje</div>
            <div>
              <span
                class="rounded-md bg-red-100 px-3 py-1 font-bold text-red-700 dark:border dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-400"
                >{{ formatPrice(expenses_due_sum) }} Kč</span
              >
            </div>
          </div>
          <div
            class="divide-y divide-gray-200 border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
          >
            <expense-row
              :expense="expense"
              v-for="expense in expenses_due"
            ></expense-row>
          </div>
        </div>
      </div>

      <div
        class="mb-4 border border-gray-200 p-5 dark:border-zinc-800 dark:bg-zinc-950"
      >
        <div class="mb-4">
          <div class="mb-2 flex justify-between">
            <div class="dark:text-zinc-400">Dnešní příjmy</div>
            <div>
              <span
                class="rounded-md bg-blue-100 px-3 py-1 font-bold text-blue-700 dark:border dark:border-blue-400/30 dark:bg-blue-400/10 dark:text-blue-400"
                >{{ formatPrice(incomes_today_sum) }} Kč</span
              >
            </div>
          </div>

          <div
            class="divide-y divide-gray-200 border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
            v-if="incomes_today.length > 0"
          >
            <income-row
              :income="income"
              v-for="income in incomes_today"
            ></income-row>
          </div>
          <div v-if="incomes_today.length === 0" class="dark:text-zinc-400">
            -
          </div>
        </div>

        <div class="">
          <div class="mb-4 flex justify-between">
            <div class="dark:text-zinc-400">Dnešní výdaje</div>
            <div>
              <span
                class="rounded-md bg-red-100 px-3 py-1 font-bold text-red-700 dark:border dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-400"
                >{{ formatPrice(expenses_today_sum) }} Kč</span
              >
            </div>
          </div>

          <div
            class="divide-y divide-gray-200 border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
            v-if="expenses_today.length > 0"
          >
            <expense-row
              :expense="expense"
              v-for="expense in expenses_today"
            ></expense-row>
          </div>

          <div v-if="expenses_today.length === 0" class="dark:text-zinc-400">
            -
          </div>
        </div>
      </div>

      <div
          class="mb-4 border border-gray-200 p-5 dark:border-zinc-800 dark:bg-zinc-950"
      >

      <div class="mb-4">
        <div class="mb-4 flex justify-between">
          <div class="dark:text-zinc-400">Nadcházející příjmy</div>
          <div>
            <span
              class="rounded-md bg-blue-100 px-3 py-1 font-bold text-blue-700 dark:border dark:border-blue-400/30 dark:bg-blue-400/10 dark:text-blue-400"
              >{{ formatPrice(incomes_upcoming_sum) }} Kč</span
            >
          </div>
        </div>

        <div
          class="divide-y divide-gray-200 border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
          v-if="incomes_upcoming.length > 0"
        >
          <income-row
            :income="income"
            v-for="income in incomes_upcoming"
          ></income-row>
        </div>
        <div v-if="incomes_upcoming.length === 0" class="dark:text-zinc-400">
          -
        </div>
      </div>

      <div class="mb-4">
        <div class="mb-4 flex justify-between">
          <div class="dark:text-zinc-400">Nadcházející výdaje</div>
          <div>
            <span
              class="rounded-md bg-red-100 px-3 py-1 font-bold text-red-700 dark:border dark:border-red-400/20 dark:bg-red-400/10 dark:text-red-400"
              >{{ formatPrice(expenses_upcoming_sum) }} Kč</span
            >
          </div>
        </div>

        <div
          class="divide-y divide-gray-200 border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
          v-if="expenses_upcoming.length > 0"
        >
          <expense-row
            :expense="expense"
            v-for="expense in expenses_upcoming"
          ></expense-row>
        </div>

        <div v-if="expenses_upcoming.length === 0" class="dark:text-zinc-400">
          -
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { DateTime } from "luxon";
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import { ExclamationCircleIcon } from "@heroicons/vue/20/solid";
import Heading from "~/components/ui/Heading.vue";

useHead({
  title: "Nadcházející platby - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const client = useSanctumClient();
const route = useRoute();

const yesterday_end = DateTime.now().minus({ days: 1 }).endOf("day").toISO();

const { data: response } = await useAsyncData("expenses_due", () =>
  client("/api/" + route.params.workspace + "/expenses", {
    method: "GET",
    params: {
      to: yesterday_end,
      payment_status: "due",
    },
  }),
);
const expenses_due = response.value.data;
const expenses_due_sum = response.value.price_sum;

const { data: response_2 } = await useAsyncData("incomes_due", () =>
  client("/api/" + route.params.workspace + "/incomes", {
    method: "GET",
    params: {
      to: yesterday_end,
      payment_status: "due",
    },
  }),
);

const incomes_due = response_2.value.data;
const incomes_due_sum = response_2.value.price_sum;

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
const expenses_today_sum = response_3.value.price_sum;

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
const incomes_today_sum = response_4.value.price_sum;

const tomorrow_start = DateTime.now().plus({ days: 1 }).startOf("day").toISO();
const month_from_now = DateTime.now().plus({ days: 31 }).startOf("day").toISO();

const { data: response_5 } = await useAsyncData("expenses_upcoming", () =>
  client("/api/" + route.params.workspace + "/expenses", {
    method: "GET",
    params: {
      from: tomorrow_start,
      to: month_from_now,
    },
  }),
);
const expenses_upcoming = response_5.value.data;
const expenses_upcoming_sum = response_5.value.price_sum;

const { data: response_6 } = await useAsyncData("incomes_upcoming", () =>
  client("/api/" + route.params.workspace + "/incomes", {
    method: "GET",
    params: {
      from: tomorrow_start,
      to: month_from_now,
    },
  }),
);

const incomes_upcoming = response_6.value.data;
const incomes_upcoming_sum = response_6.value.price_sum;

function formatPrice(value) {
  let val = (value / 1).toFixed(2).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
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
