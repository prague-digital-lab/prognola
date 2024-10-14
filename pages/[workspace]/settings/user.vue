<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <h4
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            Uživatel
          </h4>
        </div>
      </template>
    </page-content-header>

    <div class="mb-4">
      <form v-on:submit.prevent="save">
        <div
          class="mb-3 rounded-md border border-gray-200 bg-white px-5 py-5 text-gray-700 md:w-1/2"
        >
          <div>
            <div class="mb-3 flex items-center justify-between">
              <label class="block text-base font-medium text-gray-900"
                >Jméno uživatele</label
              >
            </div>
            <div class="mt-2">
              <input
                required
                v-model="user_name"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
              />
            </div>

          </div>
        </div>
            <button
              type="submit"
              class="rounded-md bg-black px-5 py-2 text-white hover:bg-gray-800"
            >
              Uložit
            </button>
      </form>
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
  title: "Uživatel - nastavení",
});

const workspace = ref(null);

const client = useSanctumClient();
const route = useRoute();

const loaded = ref(false);

const user_name = ref("");

onMounted(async () => {
  const { data } = await useAsyncData("user", () =>
    client("/api/user/", {
      method: "GET",
    }),
  );

  user_name.value = data.value.name;

  loaded.value = true;
});

async function save() {
  const { data } = await useAsyncData("user", () =>
    client("/api/user", {
      method: "PATCH",
      body: {
        name: user_name.value,
      },
    }),
  );
}
</script>
