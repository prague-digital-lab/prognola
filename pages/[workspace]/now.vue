<template>
  <div>
    <div v-if="loaded">
      <page-content-header>
        <template v-slot:title>
          <heading>Nadcházející platby</heading>
        </template>
      </page-content-header>

      <!--      <p class="dark:text-zinc-400">Aktuální zůstatek napříč účty: 0 Kč</p>-->
      <!--      <p class="mb-10 dark:text-zinc-400">Hotovost na pokladnách: 0 Kč</p>-->
      <div
        class="mb-4 flex space-x-2 rounded-md border border-gray-200 px-3 py-2 dark:border-zinc-800"
      >
        <p
          class="inline-block rounded-md p-2 py-1 text-gray-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
          @click="switchTab('incomes')"
        >
          Příjmy
        </p>
        <p
          class="inline-block rounded-md p-2 py-1 text-gray-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
          @click="switchTab('expenses')"
        >
          Výdaje
        </p>
      </div>

      <div v-if="tab === 'incomes'">
        <div
          class="mb-4 flex flex-col rounded-md border border-red-300 p-5 dark:border-zinc-800 dark:bg-zinc-950"
          v-if="incomes_due.length > 0"
        >
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
            class="divide-y divide-gray-200 border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
          >
            <income-row
              :income="income"
              v-for="income in incomes_due"
            ></income-row>
          </div>
        </div>

        <div
          class="mb-4 rounded-md border border-gray-200 p-5 dark:border-zinc-800 dark:bg-zinc-950"
        >
          <div class="mb-4 flex justify-between">
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

        <div
          class="mb-4 rounded-md border border-gray-200 p-5 dark:border-zinc-800 dark:bg-zinc-950"
        >
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
      </div>
      <div v-if="tab === 'expenses'">
        <div
          class="mb-4 flex flex-col rounded-md border border-red-300 p-5 dark:border-zinc-800 dark:bg-zinc-950"
          v-if="expenses_due.length > 0"
        >
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

        <div
          class="mb-4 rounded-md border border-gray-200 p-5 dark:border-zinc-800 dark:bg-zinc-950"
        >
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

        <div
          class="mb-4 rounded-md border border-gray-200 p-5 dark:border-zinc-800 dark:bg-zinc-950"
        >
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
import Heading from "~/components/ui/Heading.vue";
import { useObservable } from "@vueuse/rxjs";
import { liveQuery } from "dexie";
import { openDatabase } from "~/lib/dexie/db.js";

useHead({
  title: "Nadcházející platby - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const route = useRoute();
const client = useSanctumClient();

const tab = ref("incomes");

let today_start = DateTime.now().startOf("day").toJSDate();
let today_end = DateTime.now().endOf("day").toJSDate();
const yesterday_end = DateTime.now().minus({ days: 1 }).endOf("day").toJSDate();
const tomorrow_start = DateTime.now()
  .plus({ days: 1 })
  .startOf("day")
  .toJSDate();
const month_from_now = DateTime.now()
  .plus({ days: 31 })
  .startOf("day")
  .toJSDate();

const db = openDatabase(route.params.workspace);

const expenses_due = useObservable(
  liveQuery(() => {
    return db.expenses
      .where("paid_at")
      .below(today_start)
      .and((item) => {
        return (
          item.payment_status === "plan" || item.payment_status === "pending"
        );
      })
      .toArray();
  }),
);

const expenses_due_sum = computed(() => {
  return expenses_due.value.reduce(function (a, b) {
    return a + b.price;
  }, 0);
});

const incomes_due = useObservable(
  liveQuery(() => {
    return db.incomes
      .where("paid_at")
      .below(today_start)
      .and((item) => {
        return (
          item.payment_status === "plan" || item.payment_status === "pending"
        );
      })
      .toArray();
  }),
);

const incomes_due_sum = computed(() => {
  return incomes_due.value.reduce(function (a, b) {
    return a + b.amount;
  }, 0);
});

const expenses_today = useObservable(
  liveQuery(() => {
    return db.expenses
      .where("paid_at")
      .between(today_start, today_end)
      .toArray();
  }),
);

const expenses_today_sum = computed(() => {
  return expenses_today.value.reduce(function (a, b) {
    return a + b.price;
  }, 0);
});

const incomes_today = useObservable(
  liveQuery(() => {
    return db.incomes
      .where("paid_at")
      .between(today_start, today_end)
      .toArray();
  }),
);

const incomes_today_sum = computed(() => {
  return incomes_today.value.reduce(function (a, b) {
    return a + b.amount;
  }, 0);
});

const expenses_upcoming = useObservable(
  liveQuery(() => {
    return db.expenses
      .where("paid_at")
      .between(tomorrow_start, month_from_now)
      .toArray();
  }),
);

const expenses_upcoming_sum = computed(() => {
  return expenses_upcoming.value.reduce(function (a, b) {
    return a + b.price;
  }, 0);
});

const incomes_upcoming = useObservable(
  liveQuery(() => {
    return db.incomes
      .where("paid_at")
      .between(tomorrow_start, month_from_now)
      .toArray();
  }),
);

const incomes_upcoming_sum = computed(() => {
  return incomes_upcoming.value.reduce(function (a, b) {
    return a + b.amount;
  }, 0);
});

const loaded = computed(() => {
  return (
    expenses_due.value &&
    incomes_due.value &&
    expenses_today.value &&
    incomes_today.value &&
    expenses_upcoming.value &&
    incomes_upcoming.value
  );
});

onMounted(()=>{
  let route = useRoute();
  if (route.query.tab) {
    switchTab(route.query.tab);
  }
})

function formatPrice(value) {
  let val = (value / 1).toFixed(2).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function switchTab(tab_name) {
  const router = useRouter();
  tab.value = tab_name;
  router.replace({ query: { tab: tab_name } });
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
