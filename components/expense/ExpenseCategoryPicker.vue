<script>

export default {
  props: ['expense'],

  data: () => {
    return {
      expanded: false,

      expense_categories: [],
      selected_expense_category: null,
    }
  },

  mounted() {
    if (this.expense.expense_category_id) {
      // consonle.log('Je tu kategoria')
      this.selected_expense_category = this.expense.expense_category
    }

    this.loadExpenseCategories()
  },

  methods: {
    expand() {
      this.expanded = true
    },

    close() {
      this.expanded = false
    },

    async selectCategory(expense_category) {
      this.expanded = false
      this.selected_expense_category = expense_category

      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses/' + this.expense.id, {
            method: 'PATCH',
            body: {
              expense_category_id: expense_category.id
            }
          })
      )
    },

    async loadExpenseCategories() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expense-categories', {
            method: 'GET'
          })
      )

      this.expense_categories = data.value
    }
  }
}
</script>

<template>
  <div class="relative">
    <p class="text-xs px-1 py-1 rounded text-gray-800 hover:bg-gray-100 mb-7" @click="expanded ? close() : expand()">

      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
           class="size-5 text-gray-600 inline-block me-2">
        <path fill-rule="evenodd"
              d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"
              clip-rule="evenodd"/>
      </svg>


      {{ selected_expense_category ? selected_expense_category.department.name : '' }}

      <span class="mx-1 text-slate-400" v-if="selected_expense_category">></span>

      {{ selected_expense_category ? selected_expense_category.name : 'zvolit kategorii' }}
    </p>


    <Transition>
      <div v-if="expanded"
           class="absolute left-[-200px] top-[-5px] w-[197px] bg-white border-slate-200 border shadow rounded px-1 py-1 scroll-auto">

        <input class="rounded w-full mb-2" type="text" placeholder="Název kategorie...">

        <p class="px-2 py-1 hover:bg-gray-100 text-gray-700 text-sm" v-for="category in expense_categories" @click="selectCategory(category)">
          {{ category.name }}</p>
      </div>
    </Transition>

  </div>
</template>

<style scoped>

</style>