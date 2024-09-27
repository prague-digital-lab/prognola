<template>
  <div>
    <div class="border border-gray-800 bg-gray-700/20 text-gray-200 rounded-2xl px-5 py-5 text-center">
        Načítání firmy.
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'guest',
  middleware: ['sanctum:auth'],
})

useHead({
  title: 'Prognola'
})
</script>

<script>
export default {
  data: () => {
    return {
      user: null
    }
  },

  mounted() {

  },

  methods: {

  },

}
</script>
