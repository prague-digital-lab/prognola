<script>
import {
  updateExpense,
  updateExpenseFromLocalObject,
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
      await updateExpenseFromLocalObject(this.expense.uuid, this.expense);

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

<template>
  <div class="relative">
    <p
      class="mb-3 rounded px-1 py-1 text-base text-gray-700 hover:bg-gray-100 dark:text-zinc-400 dark:hover:bg-zinc-900"
      @click="expanded ? close() : expand()"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="me-2 inline-block size-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"
        />
      </svg>

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

<style scoped></style>
