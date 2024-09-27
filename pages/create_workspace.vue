<template>
  <div class="text-center">
    <h1 class="text-2xl mb-5 text-gray-200">Vytvořte novou firmu</h1>

    <div class="border border-gray-800 bg-gray-700/20 text-gray-200 rounded-2xl px-5 py-5 text-center">

      <p>Název firmy</p>

      <input v-model="name">

      <p>URL adresa firmy</p>

      <input v-model="url_slug">

      <a @click="submit">Vytvořit firmu</a>

    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'guest',
  middleware: ['sanctum:auth'],
})

useHead({
  title: 'Založení firmy - Prognola'
})
</script>

<script>
export default {
  data: () => {
    return {
      user: null,
      workspaces: [],

      name: '',
      url_slug: '',
    }
  },

  async mounted() {

    // Load available workspaces
    const client = useSanctumClient();

    const {data} = await useAsyncData('workspaces', () =>
        client('/api/workspaces', {
          method: 'GET',
        })
    )

    this.workspaces = data.value

    if (this.workspaces.length === 0) {
      await navigateTo('/create_workspace')
    }
  },

  methods: {
    async submit() {
      // Load available workspaces
      const client = useSanctumClient();

      const {data} = await useAsyncData('workspaces', () =>
          client('/api/workspaces', {
            method: 'POST',
            body: {
              url_slug: this.url_slug,
              name: this.name
            }
          })
      )

      await navigateTo(this.url_slug + '/cashflow')
    }
  }
}
</script>
