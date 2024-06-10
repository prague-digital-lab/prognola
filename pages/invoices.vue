<template>
  <div>
    <div class="md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Tržby</h2>
      </div>
      <div class="mt-4 flex md:ml-4 md:mt-0">
        <input type="date" v-model="from">
        <input type="date" v-model="to">
      </div>
    </div>


    <div v-if="loaded">
      <Line
          id="my-chart-id"
          :options="chartOptions"
          :data="chartData"
      />
    </div>
  </div>
</template>

<script>
import {CategoryScale, Chart as ChartJS, Legend, LinearScale, LineElement, PointElement, Title, Tooltip} from 'chart.js'
import {Line} from 'vue-chartjs'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)

export default {
  components: {Line},

  data() {
    return {
      loaded: false,

      from: '2023-01-01',
      to: '2024-06-30',

      chartData: {},
      chartOptions: {
        responsive: true
      }
    }
  },

  mounted() {
    this.fetchData()
  },

  methods: {
    async fetchData() {
      console.log(`http://valasskapevnost.cz.test/api/stats/income?from=${this.from}&to=${this.to}`)
      const data = await $fetch(`http://valasskapevnost.cz.test/api/stats/income?from=${this.from}&to=${this.to}`)

      this.chartData = {
        labels: toRaw(data.chart_labels),
        datasets: [{
          data: toRaw(data.chart_data_price)
        }],
      }

      this.loaded = true
    }
  }
}


</script>