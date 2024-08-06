<script>
import {DateTime} from 'luxon'
import expense from "~/pages/expenses/[expense].vue";

export default {
  props: ['expense'],

  data: () => {
    return {
      expanded: false,

      selected_expense_category: null,
    }
  },

  mounted() {
    if (this.expense.expense_category_id) {
      this.selected_expense_category = this.expense.expense_category
    }
  },

  methods: {
    expand() {
      this.expanded = true
    },

    close() {
      this.expanded = false
    },

    async updateExpenseCategory(expense_category) {
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
    }
  }
}
</script>

<template>
  <div class="relative">
    <p class="text-xs px-1 py-1 rounded text-gray-800 hover:bg-gray-100 mb-7" @click="expand">

      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
           class="size-5 text-gray-600 inline-block me-2">
        <path fill-rule="evenodd"
              d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"
              clip-rule="evenodd"/>
      </svg>

      Marketing <span class="mx-1 text-slate-400">></span>
      {{ selected_expense_category ?  selected_expense_category.name : 'přidat kategorii' }}
    </p>


    <Transition>
      <div v-if="expanded"
           class="absolute left-[-150px] top-[-5px] w-[147px] bg-white border-slate-200 border shadow rounded px-1 py-1">


        <p class="ms-2 text-sm text-gray-500">Datum úhrady</p>

      </div>
    </Transition>

  </div>
</template>

<style scoped>

</style>