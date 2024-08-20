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
                 placeholder="Název účtu" v-model="input_name" v-on:blur="updateName">


          <div>
            <!--            <bank_account-options :bank_account="bank_account"/>-->
          </div>

          <div class="mt-4 flex md:ml-4 md:mt-0">
                       <div class="me-2">
              <div class="mt-2">
                <input type="date" v-model="from"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
              </div>
            </div>

            <div>
              <div class="mt-2">
                <input type="date" v-model="to"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
              </div>
            </div>
          </div>


        </div>


        <p class="text-gray-500 text-sm mb-5">Účet</p>

        <div class="border border-gray-200 rounded divide-gray-200 divide-y mb-4">
          <bank-payment-row v-for="bank_payment in bank_payments" :bank_payment="bank_payment"/>

          <div v-if="bank_payments.length === 0" class="w-full flex items-center justify-center h-[400px]">
            <p class="text-gray-600">Žádné odpovídající platby.</p>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<script>
// import bank_accountStatusSelect from "~/components/bank_account/bank_accountStatusSelect.vue";
// import bank_accountPaidAtInput from "~/components/bank_account/bank_accountPaidAtInput.vue";

export default {
  // components: {bank_accountPaidAtInput, bank_accountStatusSelect},
  data() {
    return {
      route: null,
      loaded: false,

      bank_account: null,
      bank_payments: [],
      input_name: '',
      input_description: '',

      from: '2024-07-01',
      to: '2024-07-30',
    }
  },

  mounted() {
    this.route = useRoute()

    if (localStorage.getItem('from')) {
      this.from = localStorage.getItem('from')
    } else {
      this.from = '2024-07-01';
    }

    if (localStorage.getItem('to')) {
      this.to = localStorage.getItem('to')
    } else {
      this.to = '2024-07-30';
    }

    this.fetchData()
  },

  watch: {
    from: function (newVal, oldVal) {
      this.fetchData()
      localStorage.setItem('from', newVal)
    },
    to: function (newVal, oldVal) {
      this.fetchData()
      localStorage.setItem('to', newVal)
    },
  },

  computed: {
    title() {
      return this.bank_account ? this.bank_account.name + ' - Finance' : 'Detail platby - Prognosol Finance'
    }
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('bank_account', () =>
          client('/api/bank_accounts/' + this.route.params.bank_account, {
            method: 'GET',
          })
      )

      this.bank_account = data.value
      this.input_name = data.value.name

      const bank_payments_data = await useAsyncData('bank_payments', () =>
          client('/api/bank_payments', {
            method: 'GET',
            params: {
              bank_account_id: this.route.params.bank_account,
              from: this.from,
              to: this.to
            }
          })
      )

      this.bank_payments = bank_payments_data.data.value

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    async updateName() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('bank_account', () =>
          client('/api/bank_accounts/' + this.route.params.bank_account, {
            method: 'PATCH',
            body: {
              name: this.input_name
            }
          })
      )
    },

    async updateDescription() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('bank_account', () =>
          client('/api/bank_accounts/' + this.route.params.bank_account, {
            method: 'PATCH',
            body: {
              description: this.input_description,
            }
          })
      )
    },
  }
}

</script>
