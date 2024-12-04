<template>
  <div>
    <page-content-header>
      <template v-slot:title>
        <heading>Výdaje ke zpracování</heading>
      </template>
      <template v-slot:subtitle>
        <p class="text-base text-gray-500 dark:text-zinc-400">
          Celkem výdajů ke zpracování: {{ expenses.length }}
        </p>
      </template>
    </page-content-header>

    <div class="mb-4 md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1"></div>
    </div>

    <div
      class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 bg-white dark:bg-transparent dark:divide-zinc-800 dark:border-zinc-800"
    >
      <expense-row v-for="expense in expenses" :expense="expense"></expense-row>

      <div
        v-if="expenses.length === 0"
        class="flex h-[400px] w-full items-center justify-center bg-white dark:bg-transparent"
      >
        <p class="text-center text-base text-gray-500">
          Všechny výdaje jsou zpracované.<br />
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import Heading from "~/components/ui/Heading.vue";
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import { DateTime } from "luxon";
import { getExpensesByPaymentStatus} from "~/lib/dexie/repository/expense_repository.js";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"]
});

useHead({
  title: "Výdaje ke zpracování"
});

// Page UI data
const loaded = ref(false);
const new_expense_name = ref("");

const expenses = ref([]);
const price_sum = ref(0);

onMounted(async () => {
  fetchData();
});

// async function fetchData() {
//   const client = useSanctumClient();
//   const route = useRoute();
//
//   const { data } = await useAsyncData("expense_drafts", () =>
//     client("/api/" + route.params.workspace + "/expenses", {
//       method: "GET",
//       params: {
//         payment_status: "draft"
//       }
//     })
//   );
//
//   this.expenses = data.value.data;
//   this.price_sum = data.value.price_sum;
//
//   this.loaded = true;
// }

async function fetchData() {
  expenses.value = await getExpensesByPaymentStatus('draft');

  loaded.value = true;
}

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

async function createExpense() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("expense", () =>
    client("/api/" + route.params.workspace + "/expenses", {
      method: "POST",
      body: {
        description: new_expense_name.value,
        price: 0,
        paid_at: this.from
      }
    })
  );

  let uuid = data.value.uuid;

  await navigateTo("/" + route.params.workspace + "/expenses/" + uuid);
}


</script>
