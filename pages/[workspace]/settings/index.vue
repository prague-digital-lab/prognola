<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <h4
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            Zasílání dokladů
          </h4>
        </div>
      </template>
    </page-content-header>

    <div class="mb-4">
      <div
        class="mb-7 rounded-md border border-gray-200 bg-white px-5 py-5 text-gray-700 md:w-1/2"
      >
        <div>
          <div class="mb-3 flex items-center justify-between">
            <label class="block text-base font-medium text-gray-900"
              >Email pro zasílání přijatých dokladů</label
            >

            <Switch
              @update:modelValue="updateInboxEnabled($event)"
              v-model="enable_email_inbox"
              :class="[
                enable_email_inbox ? 'bg-indigo-600' : 'bg-gray-200',
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
              ]"
            >
              <span class="sr-only">Use setting</span>
              <span
                aria-hidden="true"
                :class="[
                  enable_email_inbox ? 'translate-x-5' : 'translate-x-0',
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                ]"
              />
            </Switch>
          </div>
          <div class="mt-2">
            <input
              disabled
              v-model="inbox_email"
              class="mb-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
            />

            <a @click="refreshInboxEmail" class="cursor-pointer"
              >Vygenerovat nový email</a
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import { Switch } from "@headlessui/vue";

definePageMeta({
  layout: "settings",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

useHead({
  title: "Nastavení organizace",
});

const workspace = ref(null);
const inbox_email = ref("");

const client = useSanctumClient();
const route = useRoute();

const loaded = ref(false);

const enable_email_inbox = ref(true);

onMounted(async () => {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/workspaces/" + route.params.workspace, {
      method: "GET",
    }),
  );

  workspace.value = data.value;
  inbox_email.value = data.value.inbox_email;
  enable_email_inbox.value = data.value.enable_email_inbox === 1;

  loaded.value = true;
});

async function updateInboxEnabled(event) {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/workspaces/" + route.params.workspace + "/inbox-settings", {
      method: "PATCH",
      body: {
        enable_email_inbox: event,
      },
    }),
  );
}

async function refreshInboxEmail() {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/workspaces/" + route.params.workspace + "/refresh-email", {
      method: "POST",
    }),
  );

  inbox_email.value = data.value.inbox_email;
}
</script>
