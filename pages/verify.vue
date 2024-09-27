<template>
  <div>
    <div class="border border-gray-800 bg-gray-700/20 text-gray-200 rounded-2xl px-5 py-5 text-center">
      <h1 class="text-3xl mb-5">Ověření emailu</h1>

      <p>Právě jsme vám poslali odkaz na email <b>{{ user ? user.email : '' }}</b>.</p>

    </div>
  </div>
</template>


<script>
export default {
  setup() {
    definePageMeta({
      layout: 'guest',
      middleware: ['sanctum:auth'],
    })

    useHead({
      title: 'Ověření účtu - Prognola'
    })
  },



  data: () => {
    return {
      user: null
    }
  },

  mounted() {
    const {user} = useSanctumAuth();
    this.user = user
  },

  methods: {},

}
</script>
