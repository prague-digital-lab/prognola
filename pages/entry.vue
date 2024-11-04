<template>
  <div>
    <div
      class="rounded-2xl border border-gray-800 bg-gray-700/20 px-5 py-5 text-center text-gray-200"
    >
      Načítání firmy.
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "guest",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

useHead({
  title: "Prognola",
});
</script>

<script>
export default {
  data: () => {
    return {
      user: null,
      workspaces: [],
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

    this.workspaces.value = data.value;

    // If user has no workspaces, redirect to workspace create guide
    if (this.workspaces.value.length === 0) {
      await navigateTo("/create_workspace");
      return;
    }

    let last_url_slug = localStorage.getItem("active_workspace_url_slug");

    let workspace_slug;
    if (last_url_slug !== null) {
      workspace_slug = last_url_slug;
    } else {
      // Redirect to first workspace
      workspace_slug = this.workspaces.value[0].url_slug;
    }

    // Redirect is made with native js location.
    // This is ugly workaround, because nuxt navigateTo
    // caused undefined workspace loaded in default.vue layout.
    location.href = "/" + workspace_slug + "/cashflow";
  },

  methods: {},
};
</script>
