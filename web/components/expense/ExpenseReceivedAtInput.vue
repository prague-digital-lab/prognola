<script>
import { DateTime } from "luxon";
import { updateExpense } from "~/lib/dexie/repository/expense_repository.js";

export default {
  props: ["expense"],

  data: () => {
    return {
      expanded: false,

      received_at: null,
    };
  },

  mounted() {
    this.received_at = DateTime.fromJSDate(this.expense.received_at).toFormat(
      "yyyy-MM-dd",
    );
  },

  methods: {
    expand() {
      this.expanded = true;
    },

    close() {
      this.expanded = false;
    },

    formatDate(date) {
      if (this.received_at === null) {
        return null;
      }

      let formatted = DateTime.fromFormat(date, "yyyy-MM-dd");

      return formatted.toFormat("d.M.yyyy");
    },

    async updateDate() {
      this.expanded = false;

      console.log("Updating");

      const client = useSanctumClient();
      const route = useRoute();

      let expense = this.expense;
      expense.received_at = DateTime.fromFormat(
        this.received_at,
        "yyyy-MM-dd",
      ).toJSDate();
      await updateExpense(this.expense.uuid, expense);

      const { data } = await useAsyncData("expense", () =>
        client(
          "/api/" + route.params.workspace + "/expenses/" + this.expense.uuid,
          {
            method: "PATCH",
            body: {
              received_at: this.received_at,
            },
          },
        ),
      );
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
        class="me-2 inline-block size-5 text-gray-600 dark:text-zinc-500"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"
        />
      </svg>

      {{ formatDate(received_at) }}
    </p>

    <Transition>
      <div
        v-if="expanded"
        class="absolute left-[-150px] top-[-5px] w-[147px] rounded border border-slate-200 bg-white px-1 py-1 shadow"
      >
        <input
          class="mb-2 w-full rounded"
          type="date"
          autofocus
          v-on:blur="updateDate"
          v-model="received_at"
        />

        <p class="ms-2 text-base text-gray-500">Datum úhrady</p>
      </div>
    </Transition>
  </div>
</template>

<style scoped></style>
