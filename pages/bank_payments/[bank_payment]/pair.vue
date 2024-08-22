<template>
  <Head>
    <Title>Párování platby</Title>
  </Head>

  <div v-if="loaded">
    <div class="md:flex md:justify-between mb-4 divide-x divide-slate-100 h-auto">
      <div class="w-full me-5">
        <h4 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight mb-4">Párování platby
          {{ bank_payment.id }}</h4>
      </div>

    </div>


    <div>
      <div class="overflow-hidden bg-white shadow sm:rounded-lg mb-4">
        <!--        <div class="px-4 py-6 sm:px-6">-->
        <!--          <h3 class="text-base font-semibold leading-7 text-gray-900">Informace o platbě</h3>-->
        <!--        </div>-->
        <div class="border-t border-gray-100">
          <dl class="divide-y divide-gray-100">
            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-900">Částka</dt>
              <dd class="mt-1 text-sm leading-6 text-red-700 font-bold sm:col-span-2 sm:mt-0">
                {{ formatPrice(bank_payment.amount) }} Kč
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-900">Účet</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ bank_payment.bank_account.name }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-900">Popis platby</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ bank_payment.description }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-900">Zpráva pro příjemce</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ bank_payment.sender_message }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-900">Externí ID</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ bank_payment.external_id }}</dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-900">Variabilní symbol</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ bank_payment.variable_symbol }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-900">Datum platby</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ formatDate(bank_payment.issued_at) }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="bank_payment.counter_account_number">
              <dt class="text-sm font-medium text-gray-900">Číslo protiúčtu</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ bank_payment.counter_account_number }}/{{ bank_payment.counter_bank_number }}
              </dd>
            </div>


            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="bank_payment.paired_at">
              <dt class="text-sm font-medium text-gray-900">Spárováno</dt>
              <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                {{ formatDate(bank_payment.paired_at) }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <h2 class="mb-2 mt-4 text-gray-700 font-semibold text-sm">Vyhledávání výdaje ke spárování</h2>

      <div class="overflow-hidden bg-white shadow sm:rounded-lg border border-gray-100 sm:px-6 mb-4 py-2">
        <input placeholder="Název výdaje" class="me-4 rounded" v-model="name_query">
        <input placeholder="Částka" class="me-4 rounded" v-model="price_query">

        <a @click="searchExpenses" class="cursor-pointer">Vyhledat výdaje</a>
      </div>


      <div class="border border-gray-200 rounded divide-gray-200 divide-y mb-4" v-if="expenses_loaded">
        <expense-row v-for="expense in expenses" :expense="expense"></expense-row>

        <div v-if="expenses.length === 0" class="w-full flex items-center justify-center h-[100px]">
          <p class="text-gray-600">Žádné odpovídající výdaje ke spárování.</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script>

import {formatDate} from "compatx";

export default {
  data() {
    return {
      route: null,
      loaded: false,

      bank_payment: null,

      price_query: '',
      name_query: '',

      expenses: [],
      expenses_loaded: false,
    }
  },

  mounted() {
    this.route = useRoute()
    this.fetchBankPayment()
  },

  methods: {
    formatDate,

    async fetchBankPayment() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('bank_payment', () =>
          client('/api/bank_payments/' + this.route.params.bank_payment, {
            method: 'GET',
          })
      )

      this.bank_payment = data.value
      this.price_query = this.bank_payment.amount * -1
      this.loaded = true

    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    async searchExpenses() {
      const client = useSanctumClient();

      let params = {
        is_paired: false,
      }

      if (this.name_query) {
        params.query = this.name_query
      }

      if (this.price_query) {
        params.price = this.price_query
      }

      const {data} = await useAsyncData('bank_payment_pairing_expenses', () =>
          client('/api/expenses', {
            method: 'GET',
            params: params
          })
      )

      this.expenses = data.value.data
      this.expenses_loaded = true;
    }
  }
}

</script>
