<template>
  <div>
    <div class="border border-gray-800 bg-gray-700/20 text-gray-200 rounded-2xl px-5 py-5 text-center">
      <h1 class="text-3xl mb-5">Ověření emailu</h1>

      <p>Právě jsme vám poslali odkaz na email <b>{{ user ? user.email : '' }}</b>.</p>

      <p class="text-gray-300">Nedošel vám email? <a @click="resend" class="text-indigo-400 cursor-pointer">Zkusit poslat znovu.</a></p>

    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'guest',
  middleware: ['sanctum:auth', 'sanctum:not-verified'],
})

useHead({
  title: 'Ověření účtu - Prognola'
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
    const {user} = useSanctumAuth();
    this.user = user
  },

  methods: {
    async resend() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('income', () =>
          client('/api/email/verify/resend', {
            method: 'POST',
          })
      )
    }
  },

}
</script>
