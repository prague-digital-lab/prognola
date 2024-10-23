<script>
export default {
  props: ["income"],

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

    async deleteIncome() {
      if (!confirm("Opravdu chcete odstranit tento příjem?")) {
        return;
      }

      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client(
          "/api/" + route.params.workspace + "/incomes/" + this.income.uuid,
          {
            method: "DELETE",
          },
        ),
      );

      await navigateTo("/" + route.params.workspace + "/income");
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
        class="absolute left-[-150px] top-[-5px] w-[147px] rounded border border-slate-200 bg-white shadow-sm"
      >
        <p
          class="px-3 py-2 text-gray-700 duration-100 hover:bg-gray-hover"
          @click="deleteIncome"
        >
          Odstranit výdaj
        </p>
      </div>
    </Transition>
  </div>
</template>

<style scoped></style>
