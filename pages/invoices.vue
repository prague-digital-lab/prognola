<template>
  <div>
    <div class="md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Tržby</h2>
      </div>
      <div class="mt-4 flex md:ml-4 md:mt-0">
        <!--      <button type="button"-->
        <!--              class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">-->
        <!--        Edit-->
        <!--      </button>-->
        <!--      <button type="button"-->
        <!--              class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">-->
        <!--        Publish-->
        <!--      </button>-->
      </div>
    </div>

    <Bar
        id="my-chart-id"
        :options="chartOptions"
        :data="chartData"
    />
  </div>
</template>

<script setup type="ts">
import {Bar} from 'vue-chartjs'
import {BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const chartData = {
  labels: ['January', 'February', 'March'],
  datasets: [{data: [100, 20, 12]}]
}
const chartOptions = {
  responsive: true
}


async function fetchData() {
  const {data: count} = await useFetch('/api/count').then((res) => {
    console.log(res.data)
  })

}
</script>