<template>
  <div class="flex flex-col items-center text-center">
    <h1 class="mb-3 text-2xl text-gray-200">Vytvořte novou firmu</h1>

    <p class="mb-5 text-gray-500">Později budete moci název změnit.</p>

    <div
      class="rounded-2xl border border-gray-800 bg-gray-700/20 px-7 py-7 text-start text-gray-200 md:w-1/2"
    >
      <p class="mb-2 text-sm text-gray-300">Název firmy</p>

      <input
        class="mb-10 w-full rounded border border-gray-500 bg-gray-900"
        v-model="name"
      />

      <p class="mb-2 text-sm text-gray-300">URL adresa firmy</p>

      <input
        class="mb-10 w-full rounded border border-gray-500 bg-gray-900"
        v-model="url_slug"
      />

      <div class="mx-5">
        <a
          @click="submit"
          class="block w-full select-none rounded bg-gray-200 px-3 py-3 text-center text-sm text-gray-800 hover:bg-white"
          >Vytvořit firmu</a
        >
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "guest",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

useHead({
  title: "Založení firmy - Prognola",
});
</script>

<script>
export default {
  data: () => {
    return {
      user: null,
      workspaces: [],

      name: "",
      url_slug: "",
    };
  },

  async mounted() {
    // Load available workspaces
    const client = useSanctumClient();

    const { data } = await useAsyncData("workspaces", () =>
      client("/api/workspaces", {
        method: "GET",
      }),
    );

    this.workspaces = data.value;

    if (this.workspaces.length === 0) {
      await navigateTo("/create_workspace");
    }
  },

  methods: {
    async submit() {
      // Load available workspaces
      const client = useSanctumClient();

      const { data } = await useAsyncData("workspaces", () =>
        client("/api/workspaces", {
          method: "POST",
          body: {
            url_slug: this.url_slug,
            name: this.name,
          },
        }),
      );

      location.href = "/" + this.url_slug + "/cashflow";
    },
  },
};
</script>
