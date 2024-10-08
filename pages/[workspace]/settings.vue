<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <h4
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            Nastavení firmy
          </h4>
        </div>
      </template>
      <template v-slot:subtitle>
        <p class="text-gray-500">{{ workspace.name }}</p>
      </template>
    </page-content-header>

    <form v-on:submit.prevent="updateInboxSettings">
      <div class="mb-4">
        <div
          class="mb-7 rounded-md border border-gray-200 bg-white px-5 py-5 text-gray-700 md:w-1/2"
        >
          <div>
            <label for="email" class="block text-base font-medium text-gray-900"
              >Email pro zasílání přijatých dokladů</label
            >
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
    </form>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";

definePageMeta({
  layout: "default",
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

onMounted(async () => {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/workspaces/" + route.params.workspace, {
      method: "GET",
    }),
  );

  workspace.value = data.value;
  inbox_email.value = data.value.inbox_email;
  loaded.value = true;
});

async function updateInboxSettings() {}

async function refreshInboxEmail() {
  const { data } = await useAsyncData("workspace", () =>
    client("/api/workspaces/" + route.params.workspace + "/refresh-email", {
      method: "POST",
    }),
  );

  inbox_email.value = data.value.inbox_email;
}
</script>
