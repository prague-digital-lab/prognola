<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <div class="md:flex md:justify-between mb-4 divide-x divide-slate-100 h-screen">
      <div class="w-full me-5 overflow-scroll">
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
          <expense-scan-row :scan="scan" v-for="scan in expense.scans"></expense-scan-row>

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

        <expense-organisation-picker :expense="expense"/>

        <p class="text-xs px-1 font-medium text-gray-500 mb-2">Kategorie výdaje</p>

        <expense-category-picker :expense="expense"/>

        <expense-payments-picker :expense="expense"/>


        <p class="text-xs px-1 font-medium text-gray-500 mb-2">Datum přijetí</p>
        <expense-received-at-input :expense="expense"/>

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
