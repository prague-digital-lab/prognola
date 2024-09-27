<template>
  <div>
    <div class="md:flex md:items-center md:justify-between mb-4">
      <div class="min-w-0 flex-1">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-4">Osoby a firmy</h4>
      </div>
    </div>

    <div class="border border-gray-200 rounded divide-gray-200 divide-y mb-4">
      <organisation-row v-for="organisation in organisations" :organisation="organisation"></organisation-row>

      <div v-if="organisations.length === 0" class="w-full flex items-center justify-center h-[400px]">
        <p class="text-gray-600">Žádné odpovídající organizace.</p>
      </div>
    </div>

    <div class="flex justify-end">
      <form @submit.prevent="createExpense">
        <input v-model="new_expense_name"
               placeholder="Nový výdaj..."
               required
               class="rounded border border-gray-200 me-2 py-1">

        <button type="submit" class="bg-indigo-700 hover:bg-indigo-900 transition rounded px-3 py-1 text-gray-100">
          Přidat
        </button>
      </form>
    </div>


  </div>
</template>

<script setup>
useHead({
  title: 'Adresář - Prognola'
})

definePageMeta({
  layout: 'default',
  middleware: ['sanctum:auth', 'sanctum:verified'],
})
</script>

<script>
export default {
  data() {
    return {
      // Page UI data
      loaded: false,
      new_expense_name: '',

      // Data table params
      grouped_by: null,
      filter_category_id: null,
      filter_organisation_id: null,

      // Data
      organisations: [],
    }
  },

  mounted() {
    this.fetchData()
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('income', () =>
          client('/api/organisations', {
            method: 'GET',
            params: {

            },
          })
      )

      this.organisations = data.value

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    async createOrganisation() {

      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/organisations', {
            method: 'POST',
            body: {
            }
          })
      )

      let id = data.value.id

      await navigateTo('/organisations/' + id)


    }
  }
}

</script>