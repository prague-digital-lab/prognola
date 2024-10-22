<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <h4
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
          >
            Uživatelé
          </h4>
        </div>
      </template>
    </page-content-header>

    <div></div>

    <div class="mb-4">
      <form v-on:submit.prevent="invite">
        <div
          class="mb-3 flex flex-col space-y-4 rounded-md border border-gray-200 bg-white px-5 py-5 text-gray-700 md:w-1/2"
        >
          <p class="text-primary">Pozvání uživatele</p>

          <div class="flex justify-between">
            <input
              required
              placeholder="Email uživatele"
              v-model="invited_user_email"
              class="me-4 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
            />

            <button
              type="submit"
              class="rounded-md bg-black px-5 py-2 text-white hover:bg-gray-800"
            >
              Pozvat
            </button>
          </div>
        </div>
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

const invited_user_email = ref("");

onMounted(async () => {
  // const { data } = await useAsyncData("user", () =>
  //   client("/api/user/", {
  //     method: "GET",
  //   }),
  // );
  //
  // user_name.value = data.value.name;

  loaded.value = true;
});

async function invite() {
  const route = useRoute()

  const { data } = await useAsyncData("user", () =>
    client("/api/" + route.params.workspace + "/users", {
      method: "POST",
      body: {
        email: invited_user_email.value,
      },
    }),
  );
}
</script>
