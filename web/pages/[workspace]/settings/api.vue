<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <h4
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            API
          </h4>
        </div>
      </template>
    </page-content-header>

    <div class="mb-4 md:w-1/2">
      <label class="mb-4 block text-base text-gray-900"
        >Osobní API tokeny</label
      >

      <div class="mb-4 rounded-md border border-gray-200 bg-white p-5">
        <p class="font-light text-gray-500">
          Pomocí API tokenů lze číst a upravovat data Prognoly z jiných
          aplikací. API tokeny jsou navázané na váš uživatelský účet a lze z
          nich přistupovat do všech vašich firem.
        </p>
      </div>

      <div
        class="mb-4 rounded-md border border-gray-200 bg-green-50 p-4"
        v-if="created_token_value"
      >
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
              API token byl vytvořen.
            </h3>
            <div class="mt-2 text-sm text-green-700">
              <div>
                Zkopírujte si nyní jeho hodnotu:
                <div
                  @click="copyToClipboard"
                  class="my-2 cursor-pointer rounded-md border border-green-300 p-2 text-gray-950"
                >
                  {{ created_token_value }}
                </div>
                Z bezpečnostních důvodů už nelze hodnotu tokenu znovu zobrazit.
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <div
          v-for="token in personal_access_tokens"
          class="mb-2 flex items-center justify-between rounded-md border border-gray-200 bg-white px-3 py-1 text-gray-700"
        >
          {{ token.name }}

          <a
            @click="revokeToken(token)"
            class="rounded-md px-1 py-1 font-light text-gray-500 hover:bg-gray-hover active:bg-gray-100"
            >Odebrat</a
          >
        </div>
      </div>

      <div>
        <form @submit.prevent="createToken">
          <input
            v-model="new_token_name"
            placeholder="Název tokenu"
            required
            class="me-2 rounded border border-gray-200 py-1 text-base"
          />

          <button
            type="submit"
            class="rounded bg-indigo-700 px-3 py-1 text-gray-100 transition hover:bg-indigo-900"
          >
            Vytvořit token
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";

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

const new_token_name = ref("");
const personal_access_tokens = ref([]);

const created_token_value = ref(null);

onMounted(async () => {
  await loadTokens();

  loaded.value = true;
});

async function loadTokens() {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/personal_access_tokens", {
      method: "GET",
    }),
  );

  personal_access_tokens.value = data.value;
}

async function createToken() {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/personal_access_tokens", {
      method: "POST",
      body: {
        name: new_token_name.value,
      },
    }),
  );

  created_token_value.value = data.value.token;
  new_token_name.value = "";
  await loadTokens();
}

async function revokeToken(token) {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/personal_access_tokens/" + token.uuid, {
      method: "DELETE",
    }),
  );

  await loadTokens();
}

function copyToClipboard() {
  navigator.clipboard.writeText(created_token_value.value);
}
</script>
