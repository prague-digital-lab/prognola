<template>
  <div>
    <div class="border border-gray-800 bg-gray-700/80 text-gray-200 rounded-2xl px-5 py-5 text-center">
      <h1 class="text-3xl mb-5">Ověřte svůj email</h1>

      <p>Vítejte v Prognole. Na email {{ user ? user.email : '' }} jsme poslali odkaz.</p>
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
