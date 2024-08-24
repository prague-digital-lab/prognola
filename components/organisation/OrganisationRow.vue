<template>
  <div class="px-3 py-2 hover:bg-slate-50 flex justify-between items-center" @click="navigateToOrganisation">
    <div class="text-sm flex">
<!--      <p class="text-gray-500 w-[60px] font-light">{{ organisation.id }}</p>-->
      <p class="w-[400px]">{{ organisation.name ? organisation.name : 'bez názvu' }}</p>
    </div>
  </div>
</template>

<script>
import {defineComponent} from 'vue'
import {DateTime} from 'luxon'

export default defineComponent({
  props: [
    'organisation'
  ],
  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },

    formatDate(date) {

      let formatted = DateTime.fromISO(date)
 r
      return formatted.toFormat('d.M.yyyy')
    },

    async navigateToOrganisation() {
      await navigateTo('/organisations/' + this.organisation.id)
    }
  }
})
</script>

<style scoped>

</style>