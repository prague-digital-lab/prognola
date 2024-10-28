<template>
  <div
    class="flex items-center justify-between bg-white px-3 py-2 hover:bg-gray-hover dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
    @click="navigateToIncome"
  >
    <div class="flex text-base">
      <p class="w-[400px] font-medium">{{ income.name }}</p>
    </div>

    <div class="flex items-center text-base font-light text-slate-600">
      <div
        v-if="organisation"
        class="me-2 flex cursor-pointer items-center rounded-md border border-gray-200 px-3 py-1 leading-5 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400"
        @click="navigateToOrganisation(organisation)"
      >
        <building-library-icon class="me-1 h-4 w-4"></building-library-icon>

        {{ organisation.name }}
      </div>

      <p class="pe-2 font-light dark:text-zinc-400">
        {{ formatDate(income.paid_at) }}
      </p>

      <div
        v-if="income.payment_status === 'paid'"
        class="w-[120px] dark:text-zinc-300"
      >
        <p class="me-2 text-end">{{ formatPrice(income.amount) }} Kč</p>
      </div>

      <div v-else-if="income.payment_status === 'draft'" class="w-[120px]">
        <p class="me-2 text-end font-semibold text-purple-800">ke zpracování</p>
      </div>

      <div v-else-if="income.payment_status === 'plan'" class="w-[120px]">
        <p class="me-2 text-end font-semibold text-yellow-500">
          ~{{ formatPrice(income.amount) }} Kč
        </p>
      </div>

      <div v-else class="w-[120px]">
        <p class="me-2 text-end font-bold text-orange-400">
          {{ formatPrice(income.amount) }} Kč
        </p>
      </div>

      <div>
        <income-status-icon :payment_status="income.payment_status" />
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { DateTime } from "luxon";
import { BuildingLibraryIcon } from "@heroicons/vue/24/outline/index.js";
import { findOrganisation } from "~/lib/dexie/repository/organisation_repository.js";

export default defineComponent({
  name: "IncomeRow",
  components: { BuildingLibraryIcon },
  props: ["income"],
  data: () => {
    return {
      organisation: null
    }
  },
  async mounted() {
    if (this.income.organisation) {
      this.organisation.value = await findOrganisation(props.expense.organisation);
    }
  },

  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    formatDate(date) {
      let formatted = DateTime.fromJSDate(date);

      return formatted.toFormat("d.M.yyyy");
    },

    async navigateToOrganisation(organisation) {
      const route = useRoute();
      await navigateTo(
        "/" + route.params.workspace + "/organisations/" + organisation.uuid,
      );
    },

    async navigateToIncome() {
      const route = useRoute();
      await navigateTo(
        "/" + route.params.workspace + "/income/" + this.income.uuid,
      );
    },
  },
});
</script>

<style scoped></style>
