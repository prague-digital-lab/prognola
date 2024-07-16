<template>
  <div>
    <div class="md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Nástěnka</h2>
      </div>
      <div class="mt-4 flex md:ml-4 md:mt-0">
        <div class="me-2">
          <!--          <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>-->
          <div class="mt-2">
            <input type="date" v-model="from"
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                   placeholder="you@example.com"/>
          </div>
        </div>

        <div>
          <!--          <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>-->
          <div class="mt-2">
            <input type="date" v-model="to"
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                   placeholder="you@example.com"/>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-5">
      <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
          <dt class="truncate text-sm font-medium text-gray-500">Tržby</dt>
          <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ formatPrice(whole_income) }} Kč</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
          <dt class="truncate text-sm font-medium text-gray-500">Výdaje</dt>
          <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ formatPrice(average_weekly) }} Kč</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
          <dt class="truncate text-sm font-medium text-gray-500">Zisk</dt>
          <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ formatPrice(average_daily) }} Kč</dd>
        </div>
      </dl>
    </div>


    <div v-if="loaded">
      <Bar
          id="my-chart-id"
          :options="chartOptions"
          :data="chartData"
      />
    </div>
  </div>
</template>

<script>
import {
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  Title,
  Tooltip
} from 'chart.js'
import {Bar} from 'vue-chartjs'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    BarElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)

export default {
  components: {Bar},

  data() {
    return {
      loaded: false,

      from: '2024-01-01',
      to: '2024-06-30',

      chartData: {},
      chartOptions: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            type: 'linear',
            position: 'right',
          },
        }
      },

      whole_income: '',
      average_daily: '',
      average_weekly: '',
    }
  },

  mounted() {
    this.fetchData()
  },

  watch: {
    from: function (newVal, oldVal) {
      this.fetchData()
    },
    to: function (newVal, oldVal) {
      this.fetchData()
    }
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('income', () =>
          client('/api/stats/cashflow', {
            method: 'GET',
            params: {
              from: this.from,
              to: this.to
            },
          })
      )

      // const data = await $fetch(`/api/stats/income?from=${this.from}&to=${this.to}`)

      this.chartData = {
        labels: data.value.chart_labels,
        datasets: [
          {
            label: 'Odhad příjmů',
            data: data.value.chart_data_income_prognosis,
            backgroundColor: [
              'rgba(51,169,255,0.48)',
            ],
            borderColor: [
              'rgb(3,99,208)',
            ],
            borderWidth: 3,
            hidden: false,
            cubicInterpolationMode: 'monotone',
            tension: 0.1
          },
          {
            label: 'Příjmy',
            data: data.value.chart_data_income,
            backgroundColor: [
              'rgb(114,143,252)',
            ],
            borderColor: [
              'rgb(4,87,147)',
            ],
            borderWidth: 3,
            hidden: false,
            cubicInterpolationMode: 'monotone',
            tension: 0.1
          },

          {
            label: 'Výdaje',
            data: data.value.chart_data_expense,
            backgroundColor:
                [
                  'rgba(255,0,0,0.56)',
                ],
            borderColor:
                [
                  'rgb(208,3,3)',
                ],
            borderWidth:
                3,
            hidden:
                false,
            cubicInterpolationMode:
                'monotone',
            tension:
                0.1
          },

          {
            label: 'Konečný zůstatek',
            data:
            data.value.chart_data_balance,
            backgroundColor:
                [
                  'rgba(0,0,0,0.56)',
                ],
            borderColor:
                [
                  'rgb(9,9,9)',
                ],
            borderWidth:
                3,
            hidden:
                false,
            cubicInterpolationMode:
                'monotone',
            tension:
                0.1,
            type:
                'line',
          },
        ]
      }

      this.whole_income = data.value.whole_income
      this.average_weekly = data.value.average_weekly
      this.average_daily = data.value.average_daily

      this.loaded = true
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    }
  }
}

</script>