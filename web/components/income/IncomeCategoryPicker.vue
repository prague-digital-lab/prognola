<template>
  <div class="relative">
    <p
      class="text-xs mb-7 rounded px-1 py-1 text-gray-500 hover:bg-gray-100"
      @click="expanded ? close() : expand()"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="currentColor"
        class="me-2 inline-block size-5 text-gray-600"
      >
        <path
          fill-rule="evenodd"
          d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"
          clip-rule="evenodd"
        />
      </svg>

      <span class="">{{
        selected_income_category
          ? selected_income_category.department.name + ":"
          : ""
      }}</span>

      {{
        selected_income_category
          ? selected_income_category.name
          : "zvolit kategorii"
      }}
    </p>

    <Transition>
      <div
        v-if="expanded"
        class="absolute left-[-200px] top-[-5px] w-[197px] scroll-auto rounded border border-slate-200 bg-white px-1 py-1 shadow"
      >
        <input
          class="mb-2 w-full rounded border-gray-200 py-1 text-gray-700 focus:border-gray-200 focus:ring-0"
          type="text"
          placeholder="Název kategorie..."
          v-model="category_name_filter"
        />

        <div class="max-h-[50vh] overflow-auto">
          <div v-for="department in filtered_categories_grouped_by_department">
            <p class="text-xs px-2 py-1 text-gray-500">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="inline-block size-4"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="m21 7.5-2.25-1.313M21 7.5v2.25m0-2.25-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3 2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75 2.25-1.313M12 21.75V19.5m0 2.25-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m-13.5 0L3 16.5v-2.25"
                />
              </svg>

              {{ department[0].department.name }}
            </p>

            <p
              class="px-2 py-1 text-base text-gray-700 hover:bg-gray-100"
              v-for="category in department"
              @click="selectCategory(category)"
            >
              {{ category.name }}
            </p>
          </div>

          <p
            class="px-2 py-1 text-base text-gray-500"
            v-if="filtered_categories.length === 0"
          >
            žádná kategorie
          </p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
export default {
  props: ["income"],

  data: () => {
    return {
      expanded: false,
      category_name_filter: null,

      income_categories: [],
      selected_income_category: null,
    };
  },

  mounted() {
    if (this.income.income_category_id) {
      this.selected_income_category = this.income.income_category;
    }

    this.loadIncomeCategories();
  },

  methods: {
    expand() {
      this.expanded = true;
    },

    close() {
      this.expanded = false;
    },

    async selectCategory(income_category) {
      this.expanded = false;
      this.selected_income_category = income_category;

      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client(
          "/api/" + route.params.workspace + "/incomes/" + this.income.id,
          {
            method: "PATCH",
            body: {
              income_category_id: income_category.id,
            },
          },
        ),
      );
    },

    async loadIncomeCategories() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client("/api/" + route.params.workspace + "/income_categories", {
          method: "GET",
        }),
      );

      this.income_categories = data.value;
    },
  },

  computed: {
    filtered_categories() {
      return this.income_categories.filter((category) => {
        // console.log(category.name, this.category_name_filter)
        return (
          !this.category_name_filter ||
          category.name
            .toLowerCase()
            .indexOf(this.category_name_filter.toLowerCase()) > -1
        );
      });
    },

    filtered_categories_grouped_by_department() {
      return this.filtered_categories.reduce(function (r, category) {
        r[category.department_id] = r[category.department_id] || [];
        r[category.department_id].push(category);
        return r;
      }, Object.create(null));
    },
  },
};
</script>

<style scoped></style>
