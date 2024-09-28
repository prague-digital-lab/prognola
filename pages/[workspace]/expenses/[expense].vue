<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <div
      class="mb-4 h-screen divide-x divide-slate-100 md:flex md:justify-between"
    >
      <div class="me-5 w-full overflow-scroll">
        <div class="flex justify-between">
          <input
            type="text"
            class="mb-3 w-full border-none p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight"
            placeholder="Název výdaje"
            v-model="input_description"
            v-on:blur="updateDescription"
          />

          <div>
            <expense-options :expense="expense" />
          </div>
        </div>

        <p class="mb-5 text-sm text-gray-500">Výdaj V-{{ expense.id }}</p>

        <textarea
          v-model="input_internal_note"
          class="mb-5 w-full border-none p-0 text-sm text-slate-700 focus:ring-0"
          placeholder="Přidat popis..."
          v-on:blur="updateInternalNote"
        ></textarea>

        <p class="mb-2 text-sm text-gray-600">Doklady</p>

        <div class="divide-y divide-slate-200 rounded border border-slate-200">
          <expense-scan-row
            :scan="scan"
            v-for="scan in expense.scans"
          ></expense-scan-row>

          <div>
            <a
              :href="
                'https://valasskapevnost.cz/admin/invoicing/received_invoices/' +
                expense.id
              "
              target="_blank"
            >
              <div class="w-full px-5 py-3 text-sm text-slate-600">
                Zobrazit v IS
              </div>
            </a>
          </div>
        </div>
      </div>

      <div class="w-[250px] ps-4">
        <expense-price-input :expense="expense" />

        <expense-status-select :expense="expense" />

        <expense-organisation-picker :expense="expense" />

        <p class="mb-2 px-1 text-xs font-medium text-gray-500">
          Kategorie výdaje
        </p>

        <expense-category-picker :expense="expense" />

        <expense-payments-picker :expense="expense" />

        <p class="mb-2 px-1 text-xs font-medium text-gray-500">Datum přijetí</p>
        <expense-received-at-input :expense="expense" />

        <p
          class="mb-2 px-1 text-xs font-medium text-gray-500"
          v-if="expense.payment_status === 'paid'"
        >
          Uhrazeno
        </p>
        <p
          class="mb-2 px-1 text-xs font-medium text-gray-500"
          v-if="expense.payment_status === 'plan'"
        >
          Plánovaná úhrada
        </p>
        <p
          class="mb-2 px-1 text-xs font-medium text-gray-500"
          v-if="expense.payment_status === 'draft'"
        >
          Plánovaná úhrada
        </p>
        <p
          class="mb-2 px-1 text-xs font-medium text-gray-500"
          v-if="expense.payment_status === 'pending'"
        >
          Plánovaná úhrada
        </p>

        <expense-paid-at-input :expense="expense" />
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>

<script>
import ExpenseStatusSelect from "~/components/expense/ExpenseStatusSelect.vue";
import ExpensePaidAtInput from "~/components/expense/ExpensePaidAtInput.vue";

export default {
  components: { ExpensePaidAtInput, ExpenseStatusSelect },
  data() {
    return {
      route: null,
      loaded: false,

      expense: null,
      input_description: "",
      input_internal_note: "",
    };
  },

  mounted() {
    this.route = useRoute();

    this.fetchData();
  },

  computed: {
    title() {
      return this.expense
        ? this.expense.description + " - Prognola"
        : "Detail výdaje - Prognola";
    },
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("expense", () =>
        client("/api/expenses/" + this.route.params.expense, {
          method: "GET",
        }),
      );

      this.expense = data.value;
      this.input_description = data.value.description;
      this.input_internal_note = data.value.internal_note;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async updateDescription() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("expense", () =>
        client("/api/expenses/" + this.route.params.expense, {
          method: "PATCH",
          body: {
            description: this.input_description,
          },
        }),
      );
    },

    async updateInternalNote() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("expense", () =>
        client("/api/expenses/" + this.route.params.expense, {
          method: "PATCH",
          body: {
            internal_note: this.input_internal_note,
          },
        }),
      );
    },
  },
};
</script>
