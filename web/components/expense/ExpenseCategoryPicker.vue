<template>
  <div class="relative">
    <p
      class="text-xs mb-7 rounded px-1 py-1 text-gray-800 hover:bg-gray-100 dark:text-zinc-400 dark:hover:bg-zinc-900"
      @click="expanded ? close() : expand()"
    >
      <tag-icon class="me-2 inline-block h-5 w-5"></tag-icon>

      <span class="">{{
        selected_expense_category
          ? selected_expense_category.department.name + ":"
          : ""
      }}</span>

      {{
        selected_expense_category
          ? selected_expense_category.name
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
  props: ["expense"],

  data: () => {
    return {
      expanded: false,
      category_name_filter: null,

      expense_categories: [],
      selected_expense_category: null,
    };
  },

  mounted() {
    if (this.expense.expense_category_id) {
      this.selected_expense_category = this.expense.expense_category;
    }

    this.loadExpenseCategories();
  },

  methods: {
    expand() {
      this.expanded = true;
    },

    close() {
      this.expanded = false;
    },

    async selectCategory(expense_category) {
      this.expanded = false;
      this.selected_expense_category = expense_category;

      const client = useSanctumClient();
      const route = useRoute();

      await client(
        "/api/" + route.params.workspace + "/expenses/" + this.expense.uuid,
        {
          method: "PATCH",
          body: {
            expense_category_id: expense_category.uuid,
          },
        },
      );
    },

    async loadExpenseCategories() {
      const client = useSanctumClient();
      const route = useRoute();

      this.expense_categories = await client(
        "/api/" + route.params.workspace + "/expense_categories",
        {
          method: "GET",
        },
      );
    },
  },

  computed: {
    filtered_categories() {
      return this.expense_categories.filter((category) => {
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

<script setup>
import { TagIcon } from "@heroicons/vue/24/outline/index.js";
</script>
