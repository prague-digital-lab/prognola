<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <h4
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            Oznámení
          </h4>
        </div>
      </template>
    </page-content-header>

    <div class="mb-4 md:w-1/2">
      <label class="mb-4 block text-base text-gray-900">Discord</label>

      <div
        class="mb-4 rounded-md border border-gray-200 bg-white p-5"
        v-if="discord_channel_id === null"
      >
        <p class="font-light text-gray-500">
          Nechte si zasílat důležitá oznámení do Discordu.
        </p>
      </div>

      <div v-if="discord_channel_id === null">
        <form @submit.prevent="saveDiscord">
          <input
            v-model="discord_channel_input"
            placeholder="ID Discord kanálu"
            required
            class="me-2 rounded border border-gray-200 py-1 text-base"
          />

          <button
            type="submit"
            class="rounded bg-indigo-700 px-3 py-1 text-gray-100 transition hover:bg-indigo-900"
          >
            Uložit
          </button>
        </form>
      </div>

      <div v-else>
        <div class="mb-4 rounded-md border border-gray-200 bg-green-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-green-400"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
                data-slot="icon"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800">
                Notifikace na Discord jsou aktivní.
              </h3>
              <div class="mt-2 text-sm text-green-700">
                <div>
                  Notifikace se zasílají do Discord kanálu s ID:
                  {{ discord_channel_id }}.
                </div>
              </div>
            </div>
          </div>
        </div>

        <button-secondary @click="stopDiscordNotifications()"
          >Vypnout notifikace
        </button-secondary>
      </div>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";

definePageMeta({
  layout: "settings",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

useHead({
  title: "Nastavení organizace",
});

const client = useSanctumClient();
const route = useRoute();

const loaded = ref(false);

const discord_channel_input = ref("");
const discord_channel_id = ref("");

onMounted(async () => {
  await loadSettings();

  loaded.value = true;
});

async function loadSettings() {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/workspaces/" + route.params.workspace, {
      method: "GET",
    }),
  );

  discord_channel_id.value = data.value.discord_channel_id;
}

async function saveDiscord() {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/workspaces/" + route.params.workspace + "/discord-settings", {
      method: "PATCH",
      body: {
        discord_channel_id: discord_channel_input.value,
      },
    }),
  );

  await loadSettings();
}

async function stopDiscordNotifications() {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/workspaces/" + route.params.workspace + "/discord-settings", {
      method: "PATCH",
      body: {
        discord_channel_id: null,
      },
    }),
  );

  discord_channel_input.value = "";
  discord_channel_id.value = "";

  await loadSettings();
}
</script>
