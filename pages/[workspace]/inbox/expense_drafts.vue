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
      class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 bg-white dark:divide-zinc-800 dark:border-zinc-800"
      v-if="grouped_by === null"
    >
      <expense-row v-for="expense in expenses" :expense="expense"></expense-row>

      <div
        v-if="expenses.length === 0"
        class="flex h-[400px] w-full items-center justify-center bg-white"
      >
        <p class="text-center text-base text-gray-500">
          Všechny výdaje jsou zpracované.<br />
        </p>
      </div>
    </div>

    <div class="flex justify-end">
      <form @submit.prevent="createExpense">
        <input
          v-model="new_expense_name"
          placeholder="Nový výdaj..."
          required
          class="me-2 rounded border border-gray-200 py-1 text-base"
        />

        <button
          type="submit"
          class="rounded bg-indigo-700 px-3 py-1 text-gray-100 transition hover:bg-indigo-900"
        >
          Přidat
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import Heading from "~/components/ui/Heading.vue";
import PageContentHeader from "~/components/ui/PageContentHeader.vue";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>

<script>
export default {
  data() {
    return {
      // Page UI data
      loaded: false,
      new_expense_name: "",

      // Data table params
      grouped_by: null,
      filter_category_id: null,
      filter_organisation_id: null,

      // Data
      expenses: [],
      price_sum: 0,
    };
  },

  mounted() {
    this.fetchData();
  },

  computed: {
    expenses_by_category() {
      return this.expenses.reduce(function (r, expense) {
        r[expense.expense_category_id] = r[expense.expense_category_id] || [];
        r[expense.expense_category_id].push(expense);
        return r;
      }, Object.create(null));
    },
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("expense_drafts", () =>
        client("/api/" + route.params.workspace + "/expenses", {
          method: "GET",
          params: {
            payment_status: "draft",
          },
        }),
      );

      this.expenses = data.value.data;
      this.price_sum = data.value.price_sum;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async createExpense() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("expense", () =>
        client("/api/" + route.params.workspace + "/expenses", {
          method: "POST",
          body: {
            description: this.new_expense_name,
            price: 0,
            paid_at: this.from,
          },
        }),
      );

      let uuid = data.value.uuid;

      await navigateTo("/" + route.params.workspace + "/expenses/" + uuid);
    },
  },
};
</script>
