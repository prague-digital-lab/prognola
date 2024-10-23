<template>
  <div class="flex flex-col items-center text-center">
    <h1 class="mb-3 text-2xl text-gray-200">Vítejte v Prognole</h1>

    <p class="mb-5 text-gray-400">
      Někdo vás pozval do následujících organizací
    </p>

    <div
      class="flex w-1/2 select-none flex-col space-y-4 rounded-2xl border border-gray-800 bg-gray-700/20 px-5 py-5 text-center text-gray-200"
    >
      <div
        class="flex items-center justify-between rounded-md border border-gray-700 px-5 py-2"
        v-for="workspace in workspaces"
      >
        <div>{{ workspace.name }}</div>
        <div
          class="cursor-pointer rounded-md border border-gray-800 bg-gray-700 px-3 py-1 text-sm"
          @click="joinWorkspace(workspace)"
        >
          Připojit se
        </div>
      </div>

      <nuxt-link href="/create_workspace" class="text-sm"
        >+ Vytvořit novou organizaci
      </nuxt-link>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "guest",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

useHead({
  title: "Volba organizace - Prognola",
});

const client = useSanctumClient();

const { data: workspaces } = await useAsyncData("workspaces", () =>
  client("/api/workspaces", {
    method: "GET",
    query: {
      role: "invited",
    },
  }),
);

async function joinWorkspace(workspace) {
  await useAsyncData("workspaces", () =>
    client("/api/" + workspace.url_slug + "/accept-invite", {
      method: "POST",
    }),
  );

  await navigateTo("/" + workspace.url_slug + "/cashflow");
}

onMounted(async () => {
  if (workspaces.value.length === 0) {
    await navigateTo("/create_workspace");
  }
});
</script>
