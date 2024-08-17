<script>

export default {
  props: ['expense'],

  data: () => {
    return {
      expanded: false,
      organisation_name_filter: null,

      organisations: [],
      selected_organisation: null,
    }
  },

  mounted() {
    if (this.expense.organisation_id) {
      this.selected_organisation = this.expense.organisation
    }

    this.loadOrganisations()
  },

  methods: {
    expand() {
      this.expanded = true
    },

    close() {
      this.expanded = false
    },

    async selectOrganisation(organisation) {
      this.expanded = false
      this.selected_organisation = organisation

      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses/' + this.expense.id, {
            method: 'PATCH',
            body: {
              organisation_id: organisation.id
            }
          })
      )
    },

    async loadOrganisations() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('organisations', () =>
          client('/api/organisations', {
            method: 'GET'
          })
      )

      this.organisations = data.value
    }
  },

  computed: {
    filtered_organisations() {
      return this.organisations.filter(organisation => {
        return !this.organisation_name_filter || organisation.name.toLowerCase().indexOf(this.organisation_name_filter.toLowerCase()) > -1
      })
    },
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

      {{ selected_organisation ? selected_organisation.name : 'zvolit organizaci' }}
    </p>


    <Transition>
      <div v-if="expanded"
           class="absolute left-[-200px] top-[-5px] w-[197px]  bg-white border-slate-200 border shadow rounded px-1 py-1 scroll-auto">

        <input class="rounded w-full mb-2 border-gray-200 focus:ring-0 focus:border-gray-200 text-gray-700 py-1"
               type="text" placeholder="Název organizace..." v-model="organisation_name_filter">

        <div class="max-h-[50vh] overflow-auto">
          <p class="px-2 py-1 hover:bg-gray-100 text-gray-700 text-sm" v-for="organisation in filtered_organisations"
             @click="selectOrganisation(organisation)">
            {{ organisation.name }}</p>

          <p class="px-2 py-1 text-gray-500 text-sm" v-if="filtered_organisations.length === 0">
            žádná organizace</p>
        </div>
      </div>
    </Transition>

  </div>
</template>

<style scoped>

</style>