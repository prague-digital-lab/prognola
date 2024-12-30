<template>
  <div class="relative">
    <p
      class="mb-3 rounded px-1 py-1 text-base text-gray-700 hover:bg-gray-100 dark:text-zinc-400 dark:hover:bg-zinc-900"
      @click="expanded ? close() : expand()"
    >
      <FingerPrintIcon class="me-2 inline-block h-5 w-5"> </FingerPrintIcon>

      {{ variable_symbol }}
    </p>

    <Transition>
      <div
        v-if="expanded"
        class="absolute left-[-150px] top-[-5px] w-[147px] rounded border border-slate-200 bg-white px-1 py-1 shadow"
      >
        <input
          class="mb-2 w-full rounded"
          type="number"
          autofocus
          v-on:blur="updateVariableSymbol"
          v-model="variable_symbol"
        />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { FingerPrintIcon } from "@heroicons/vue/24/outline/index.js";
</script>

<script>
import {
  updateExpense,
} from "~/lib/dexie/repository/expense_repository.js";

export default {
  props: ["expense"],

  data: () => {
    return {
      expanded: false,

      variable_symbol: null,
    };
  },

  mounted() {
    this.variable_symbol = this.expense.variable_symbol;
  },
  methods: {
    expand() {
      this.expanded = true;
    },

    close() {
      this.expanded = false;
    },

    async updateVariableSymbol() {
      this.expanded = false;

      const client = useSanctumClient();
      const route = useRoute();

      this.expense.variable_symbol = this.variable_symbol;
      await updateExpense(this.expense.uuid, this.expense);

      const { data } = await useAsyncData("expense", () =>
        client(
          "/api/" + route.params.workspace + "/expenses/" + this.expense.uuid,
          {
            method: "PATCH",
            body: {
              variable_symbol: this.variable_symbol,
            },
          },
        ),
      );

      this.$emit("expense-updated");
    },
  },
};
</script>
