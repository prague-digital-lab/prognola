<script>
import {DateTime} from 'luxon'

export default {
  props: ['expense'],

  data: () => {
    return {
      expanded: false,

      paid_at: null,
    }
  },

  mounted() {
    this.paid_at = DateTime.fromISO(this.expense.paid_at).toFormat('yyyy-MM-dd')
  },

  methods: {
    expand() {
      this.expanded = true
    },

    close() {
      this.expanded = false
    },

    formatDate(date) {

      if (this.paid_at === null) {
        return null
      }

      let formatted = DateTime.fromFormat(date, 'yyyy-MM-dd')

      return formatted.toFormat('d.M.yyyy')
    },

    async updateDate() {
      this.expanded = false

      console.log('Updating')

      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses/' + this.expense.id, {
            method: 'PATCH',
            body: {
              paid_at: this.paid_at
            }
          })
      )
    }
  }
}
</script>

<template>
  <div class="relative">
    <p class="text-sm py-1 px-1 rounded text-gray-700 hover:bg-gray-100 mb-3" @click="expanded ? close() : expand()">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
           class="size-5 inline-block me-2 text-gray-600">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
      </svg>

      {{ formatDate(paid_at) }}
    </p>


    <Transition>
      <div v-if="expanded"
           class="absolute left-[-150px] top-[-5px] w-[147px] bg-white border-slate-200 border shadow rounded px-1 py-1">

        <input class="rounded w-full mb-2" type="date" autofocus v-on:blur="updateDate" v-model="paid_at">

        <p class="ms-2 text-sm text-gray-500">Datum úhrady</p>

      </div>
    </Transition>

  </div>
</template>

<style scoped>

</style>