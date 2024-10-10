<template>
  <div>
    <div class="mb-4 md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Výdaje
        </h4>

        <p class="text-base text-gray-500">
          Celkem: {{ formatPrice(price_sum) }} Kč
        </p>
      </div>

      <div class="mt-4 flex md:ml-4 md:mt-0">
        <div class="mt-3">
        <button-secondary @click="downloadExport">Export</button-secondary>
        </div>

        <div class="me-2">
          <div class="mt-2">
            <select
              v-model="grouped_by"
              class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
            >
              <option :value="null">Seskupit</option>
              <option value="expense_category">Kategorie</option>
            </select>
          </div>
        </div>

        <div class="me-2">
          <div class="mt-2">
            <input
              type="date"
              v-model="from"
              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
            />
          </div>
        </div>

        <div>
          <div class="mt-2">
            <input
              type="date"
              v-model="to"
              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
            />
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="expenses.length === 0"
      class="mb-4 flex h-[400px] w-full items-center justify-center rounded-md border border-gray-200"
    >
      <p class="text-gray-600">Žádné odpovídající výdaje.</p>
    </div>

    <div v-if="expenses.length > 0">
      <div
        class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 bg-white"
        v-if="grouped_by === null"
      >
        <expense-row
          v-for="expense in expenses"
          :expense="expense"
        ></expense-row>
      </div>

      <div
        v-if="grouped_by === 'expense_category'"
        class="mb-4 rounded border border-gray-200"
      >
        <div
          class="divide-y divide-gray-200"
          v-for="category in expenses_by_category"
        >
          <div class="w-full bg-slate-100 py-2 ps-3 text-base text-gray-600">
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
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";

useHead({
  title: "Výdaje - Prognola",
});

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
      from: "2024-07-01",
      to: "2024-07-30",
      grouped_by: null,
      filter_category_id: null,
      filter_organisation_id: null,

      // Data
      expenses: [],
      price_sum: 0,
    };
  },

  mounted() {
    if (localStorage.getItem("from")) {
      this.from = localStorage.getItem("from");
    } else {
      this.from = "2024-07-01";
    }

    if (localStorage.getItem("to")) {
      this.to = localStorage.getItem("to");
    } else {
      this.to = "2024-07-30";
    }

    console.log(localStorage.getItem("expenses.grouped_by"));
    if (localStorage.getItem("expenses.grouped_by")) {
      this.grouped_by = localStorage.getItem("expenses.grouped_by");
    } else {
      this.grouped_by = null;
    }

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

  watch: {
    from: function (newVal, oldVal) {
      this.fetchData();
      localStorage.setItem("from", newVal);
    },

    to: function (newVal, oldVal) {
      this.fetchData();
      localStorage.setItem("to", newVal);
    },

    grouped_by: function (newVal, oldVal) {
      localStorage.setItem("expenses.grouped_by", newVal);

      if (newVal === null) {
        localStorage.removeItem("expenses.grouped_by");
      }
    },
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("expenses", () =>
        client("/api/" + route.params.workspace + "/expenses", {
          method: "GET",
          params: {
            from: this.from,
            to: this.to,
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

    async downloadExport() {
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
    }
  },
};
</script>
