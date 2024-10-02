<script>
export default {
  props: ["expense"],

  data: () => {
    return {
      expanded: false,
    };
  },

  methods: {
    expand() {
      this.expanded = true;
    },

    close() {
      this.expanded = false;
    },

    async deleteExpense() {
      if (!confirm("Opravdu chcete odstranit tento výdaj?")) {
        return;
      }

      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("expense", () =>
        client(
          "/api/" + route.params.workspace + "/expenses/" + this.expense.uuid,
          {
            method: "DELETE",
          },
        ),
      );

      await navigateTo("/" + route.params.workspace + "/expenses");
    },
  },
};
</script>

<template>
  <div class="relative">
    <p
      class="mb-3 rounded px-1 py-1 text-base text-gray-700 hover:bg-gray-100"
      @click="expanded ? close() : expand()"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="size-6"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
        />
      </svg>
    </p>

    <Transition>
      <div
        v-if="expanded"
        class="absolute left-[-150px] top-[-5px] w-[147px] rounded border border-slate-200 bg-white px-1 py-1 shadow"
      >
        <a
          :href="
            'https://valasskapevnost.cz/admin/invoicing/received_invoices/' +
            expense.id
          "
          target="_blank"
        >
          <div class="w-full p-2 text-base text-slate-600 hover:bg-gray-100">
            Zobrazit v IS
          </div>
        </a>

        <p
          class="p-2 text-base text-red-600 hover:bg-gray-100"
          @click="deleteExpense"
        >
          Odstranit výdaj
        </p>
      </div>
    </Transition>
  </div>
</template>

<style scoped></style>
