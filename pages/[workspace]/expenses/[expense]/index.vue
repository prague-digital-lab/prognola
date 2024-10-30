<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <div
      class="mb-4 h-screen divide-x divide-slate-100 md:flex md:justify-between dark:divide-zinc-800"
    >
      <div class="me-5 w-full overflow-scroll">
        <div class="flex justify-between">
          <input
            type="text"
            class="mb-3 w-full border-none bg-transparent p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight dark:text-zinc-200"
            placeholder="Název výdaje"
            v-model="input_description"
            v-on:blur="updateDescription"
          />

          <div>
            <expense-options :expense="expense" />
          </div>
        </div>

        <p class="mb-5 text-base text-gray-500">Výdaj</p>

        <textarea
          v-model="input_internal_note"
          class="mb-5 w-full border-none bg-transparent p-0 text-base text-slate-700 focus:ring-0 dark:text-zinc-300"
          placeholder="Přidat popis..."
          v-on:blur="updateInternalNote"
        ></textarea>

        <!--        <div v-if="expense.scans.length > 0">-->
        <p class="mb-2 text-base text-gray-600 dark:text-zinc-400">Přílohy</p>

        <div>
          <expense-scan-row
            :scan="scan"
            v-for="scan in scans"
          ></expense-scan-row>
        </div>

        <input
          @change="uploadFile($event)"
          type="file"
          class="mt-2 rounded-md border bg-white px-2 py-1 text-sm text-gray-800 shadow-sm duration-75 hover:bg-gray-hover active:bg-gray-100 dark:border dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:bg-zinc-800"
        />
      </div>

      <div class="w-[250px] ps-4 dark:text-zinc-400">
        <expense-price-input @expense-updated="fetchData" :expense="expense" />

        <expense-status-select
          :expense="expense"
          @expense-updated="fetchData"
        />

        <expense-organisation-picker :expense="expense" />

        <p class="mb-2 px-1 text-sm font-medium text-gray-500">
          Kategorie výdaje
        </p>

        <expense-category-picker :expense="expense" />

        <expense-payments-picker
          :expense="expense"
          :bank_payments="bank_payments"
          @expense-updated="fetchData"
        />

        <div v-if="expense.payment_status !== 'plan'">
          <p class="mb-2 px-1 text-sm font-medium text-gray-500">
            Datum přijetí
          </p>
          <expense-received-at-input :expense="expense" />
        </div>

        <div class="transition duration-100">
          <p
            class="mb-2 px-1 text-sm font-medium text-gray-500"
            v-if="expense.payment_status === 'paid'"
          >
            Uhrazeno
          </p>
          <p
            class="mb-2 px-1 text-sm font-medium text-gray-500"
            v-if="expense.payment_status === 'plan'"
          >
            Plánovaná úhrada
          </p>
          <p
            class="mb-2 px-1 text-sm font-medium text-gray-500"
            v-if="expense.payment_status === 'draft'"
          >
            Plánovaná úhrada
          </p>
          <p
            class="mb-2 px-1 text-sm font-medium text-gray-500"
            v-if="expense.payment_status === 'pending'"
          >
            Plánovaná úhrada
          </p>
        </div>

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
import { getExpense, updateExpense } from "~/lib/dexie/repository/expense_repository.js";

export default {
  components: { ExpensePaidAtInput, ExpenseStatusSelect },
  data() {
    return {
      loaded: false,

      expense: null,
      input_description: "",
      input_internal_note: "",

      scans: [],
      bank_payments: [],
    };
  },

  mounted() {
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
      const route = useRoute();

      this.expense = await getExpense(route.params.expense);
      console.log(this.expense)

      const { data } = await useAsyncData("expense", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/expenses/" +
            route.params.expense,
          {
            method: "GET",
          },
        ),
      );

      // this.expense = data.value;
      this.input_description = this.expense.description;
      this.input_internal_note = this.expense.internal_note;
      this.scans = data.value.scans;
      this.bank_payments = data.value.bank_payments;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async updateDescription() {
      const client = useSanctumClient();
      const route = useRoute();

      this.expense.description = this.input_description;
      await updateExpense(this.expense.uuid, this.expense);

      const { data } = await useAsyncData("expense", () =>
        client(
          "/api/" + route.params.workspace + "/expenses/" + this.expense.uuid,
          {
            method: "PATCH",
            body: {
              description: this.input_description,
            },
          },
        ),
      );


    },

    async updateInternalNote() {
      const client = useSanctumClient();
      const route = useRoute();

      this.expense.internal_note = this.input_internal_note;
      await updateExpense(this.expense.uuid, this.expense);

      const endpoint =
        "/api/" + route.params.workspace + "/expenses/" + this.expense.uuid;

      const { data } = await useAsyncData("expense", () =>
        client(endpoint, {
          method: "PATCH",
          body: {
            internal_note: this.input_internal_note,
          },
        }),
      );
    },

    async uploadFile(event) {
      const client = useSanctumClient();
      const route = useRoute();

      const endpoint =
        "/api/" +
        route.params.workspace +
        "/expenses/" +
        this.expense.uuid +
        "/scans";

      let formData = new FormData();
      formData.append("file", event.target.files[0]);

      const { data } = await useAsyncData("expense", () =>
        client(endpoint, {
          method: "POST",
          body: formData,
        }),
      );

      await this.fetchData();
    },
  },
};
</script>
