<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <div class="md:flex md:justify-between mb-4 divide-x divide-slate-100 h-auto">
      <div class="w-full me-5">
        <div class="flex justify-between">
          <input type="text"
                 class="p-0 w-full text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-3 border-none focus:ring-0"
                 placeholder="Název výdaje" v-model="input_description" v-on:blur="updateDescription">

          <div>
            <expense-options :expense="expense"/>
          </div>

        </div>

        <p class="text-gray-500 text-sm mb-5">Výdaj V-{{ expense.id }}</p>

        <textarea v-model="input_internal_note" class="border-none focus:ring-0 p-0 text-sm text-slate-700 w-full mb-5"
                  placeholder="Přidat popis..." v-on:blur="updateInternalNote"></textarea>

        <p class="text-sm text-gray-600 mb-2">Doklady</p>

        <div class="border border-slate-200 rounded divide-y divide-slate-200">
          <div class="w-full px-5 py-3 text-sm text-slate-600" v-for="scan in expense.scans">
            <a :href="'https://valasskapevnost.cz/admin/invoicing/received_invoices/' + expense.id" target="_blank">
              {{ scan.title }}
            </a>
          </div>

          <div>
            <a :href="'https://valasskapevnost.cz/admin/invoicing/received_invoices/' + expense.id" target="_blank">
              <div class="w-full px-5 py-3 text-sm text-slate-600">Zobrazit v IS</div>
            </a>
          </div>
        </div>
      </div>

      <div class="w-[250px] ps-4">

        <expense-price-input :expense="expense"/>

        <expense-status-select :expense="expense"/>

        <p class="text-sm py-1 px-1 rounded text-gray-700 hover:bg-gray-100 mb-4" v-if="expense.organisation">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor"
               class="size-5 inline-block me-2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
          </svg>

          {{ expense.organisation.name }}
        </p>

        <p class="text-sm py-1 px-1 rounded text-gray-500 hover:bg-gray-100 mb-7" v-else>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor"
               class="size-5 inline-block me-2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
          </svg>

          zvolit organizaci
        </p>

        <p class="text-xs px-1 font-medium text-gray-500 mb-2">Kategorie výdaje</p>

        <expense-category-picker :expense="expense"/>

        <!--        <p class="text-xs px-1 font-medium text-gray-500 mb-2">Platby</p>-->

        <!--        <p class="text-xs py-1 px-1 rounded text-gray-800 hover:bg-gray-100 mb-1">-->
        <!--          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"-->
        <!--               class="size-5 text-gray-600 inline-block me-2">-->
        <!--            <path fill-rule="evenodd"-->
        <!--                  d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"-->
        <!--                  clip-rule="evenodd"/>-->
        <!--          </svg>-->

        <!--          -2000 Kč-->
        <!--        </p>-->

        <!--        <p class="text-xs py-1 px-1 rounded text-gray-800 hover:bg-gray-100 mb-4">-->
        <!--          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"-->
        <!--               class="size-5 text-gray-600 inline-block me-2">-->
        <!--            <path fill-rule="evenodd"-->
        <!--                  d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"-->
        <!--                  clip-rule="evenodd"/>-->
        <!--          </svg>-->

        <!--          -543 Kč-->
        <!--        </p>-->

        <p class="text-xs px-1 font-medium text-gray-500 mb-2" v-if="expense.payment_status === 'paid'">Uhrazeno</p>
        <p class="text-xs px-1 font-medium text-gray-500 mb-2" v-if="expense.payment_status === 'plan'">Plánovaná
          úhrada</p>
        <p class="text-xs px-1 font-medium text-gray-500 mb-2" v-if="expense.payment_status === 'draft'">Plánovaná
          úhrada</p>
        <p class="text-xs px-1 font-medium text-gray-500 mb-2" v-if="expense.payment_status === 'pending'">Plánovaná
          úhrada</p>

        <expense-paid-at-input :expense="expense"/>

      </div>

    </div>
  </div>
</template>

<script>
import ExpenseStatusSelect from "~/components/expense/ExpenseStatusSelect.vue";
import ExpensePaidAtInput from "~/components/expense/ExpensePaidAtInput.vue";

export default {
  components: {ExpensePaidAtInput, ExpenseStatusSelect},
  data() {
    return {
      route: null,
      loaded: false,

      expense: null,
      input_description: '',
      input_internal_note: '',
    }
  },

  mounted() {
    this.route = useRoute()

    this.fetchData()
  },

  computed: {
    title() {
      return this.expense ? this.expense.description + ' - Finance' : 'Detail výdaje - Finance'
    }
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses/' + this.route.params.expense, {
            method: 'GET',
          })
      )

      this.expense = data.value
      this.input_description = data.value.description
      this.input_internal_note = data.value.internal_note

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    async updateDescription() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses/' + this.route.params.expense, {
            method: 'PATCH',
            body: {
              description: this.input_description
            }
          })
      )
    },

    async updateInternalNote() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('expense', () =>
          client('/api/expenses/' + this.route.params.expense, {
            method: 'PATCH',
            body: {
              internal_note: this.input_internal_note,
            }
          })
      )
    },
  }
}

</script>
