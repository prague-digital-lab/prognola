<template>
  <div>
    <page-content-header>
      <template v-slot:title>
        <heading>Příjmy</heading>
      </template>
      <template v-slot:subtitle>
        <div class="flex justify-between items-center">
          <p class="text-base text-gray-500 dark:text-zinc-400">
            Celkem: {{ formatPrice(price_sum) }} Kč
          </p>

          <button-primary @click="openModal" class="block md:hidden">+ nový příjem</button-primary>
        </div>
      </template>

      <template v-slot:controls>
        <div class="mt-4 flex md:ml-4 md:mt-0">
<!--          <div class="me-2">-->
<!--            <div class="mt-2">-->
<!--              <select-->
<!--                v-model="grouped_by"-->
<!--                class="block rounded-md border border-gray-200 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"-->
<!--              >-->
<!--                <option :value="null">Seskupit</option>-->
<!--                <option value="income_category">Kategorie</option>-->
<!--              </select>-->
<!--            </div>-->
<!--          </div>-->

          <div class="me-2">
            <input
              type="date"
              v-model="from"
              class="block w-full rounded-md border border-gray-200 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
            />
          </div>

          <div class="me-2">
            <input
              type="date"
              v-model="to"
              class="block w-full rounded-md border border-gray-200 py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
            />
          </div>

          <button-primary @click="openModal" class="hidden md:flex">+ nový příjem</button-primary>
        </div>
      </template>
    </page-content-header>

    <div
      class="mb-4 divide-y divide-gray-200 rounded border border-gray-200 dark:divide-zinc-800 dark:border-zinc-800"
      v-if="incomes.length > 0 && grouped_by === null"
    >
      <income-row v-for="income in incomes" :income="income"></income-row>
    </div>

    <div
      v-if="incomes.length > 0 && grouped_by === 'income_category'"
      class="mb-4 rounded border border-gray-200 dark:border-zinc-800"
    >
      <div
        class="divide-y divide-gray-200 dark:divide-zinc-800"
        v-for="category in incomes_by_category"
      >
        <div
          class="w-full bg-slate-100 py-1 ps-3 text-base text-gray-600 dark:bg-zinc-700 dark:text-zinc-200"
        >
          {{
            category[0].income_category
              ? category[0].income_category.name
              : "Příjmy bez kategorie"
          }}
        </div>
        <income-row v-for="income in category" :income="income"></income-row>
      </div>
    </div>

    <div
      v-if="incomes.length === 0"
      class="mb-4 flex h-[400px] w-full items-center justify-center rounded-md border border-gray-200 dark:border-zinc-800"
    >
      <p class="text-gray-600 dark:text-zinc-400">Žádné odpovídající příjmy.</p>
    </div>

    <div class="flex justify-end">
      <button-secondary @click="openModal">+ přidat příjem</button-secondary>
    </div>

    <income-create-modal ref="modal_create" @income-created="fetchData" />
  </div>
</template>

<script setup>
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";
import IncomeCreateModal from "~/components/ui/modals/income_create_modal/IncomeCreateModal.vue";
import Heading from "~/components/ui/Heading.vue";
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import ButtonPrimary from "~/components/ui/ButtonPrimary.vue";

useHead({
  title: "Příjmy",
});

const modal_create = useTemplateRef("modal_create");

function openModal() {
  modal_create.value.openModal();
}
</script>

<script>
import { DateTime } from "luxon";
import { getExpensesByPaidAt } from "~/lib/dexie/repository/expense_repository.js";
import { getIncomesByPaidAt } from "~/lib/dexie/repository/income_repository.js";

export default {
  data() {
    return {
      // Page UI data
      loaded: false,
      new_income_name: "",

      // Data table params
      from: "2024-07-01",
      to: "2024-07-30",
      grouped_by: null,
      filter_category_id: null,
      filter_organisation_id: null,

      // Data
      incomes: [],
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

    console.log(localStorage.getItem("incomes.grouped_by"));
    if (localStorage.getItem("incomes.grouped_by")) {
      this.grouped_by = localStorage.getItem("incomes.grouped_by");
    } else {
      this.grouped_by = null;
    }

    this.fetchData();
  },

  computed: {
    incomes_by_category() {
      return this.incomes.reduce(function (r, income) {
        r[income.income_category_id] = r[income.income_category_id] || [];
        r[income.income_category_id].push(income);
        return r;
      }, Object.create(null));
    },

    price_sum() {
      return this.incomes.reduce(function (a, b) {
        return a + b.amount;
      }, 0);
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
      localStorage.setItem("incomes.grouped_by", newVal);

      if (newVal === null) {
        localStorage.removeItem("incomes.grouped_by");
      }
    },
  },

  methods: {
    async fetchData() {
      let date_from = DateTime.fromISO(this.from).startOf("day").toJSDate();
      let date_to = DateTime.fromISO(this.to).endOf("day").toJSDate();

      this.incomes = await getIncomesByPaidAt(date_from, date_to);

      console.log(this.incomes);
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },
  },
};
</script>
