<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <input
          type="text"
          class="w-full border-none bg-transparent p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight dark:text-zinc-200"
          placeholder="Název"
          v-model="input_name"
          v-on:blur="updateName"
        />
      </template>
      <template v-slot:subtitle>
        <organisation-label :organisation="organisation"></organisation-label>
      </template>
    </page-content-header>

    <div class="mb-4 md:flex md:justify-between">
      <div class="me-5 w-full">
        <textarea
          v-model="input_internal_note"
          class="mb-5 w-full border-none bg-transparent p-0 text-base text-slate-700 focus:ring-0 dark:text-zinc-300"
          placeholder="Přidat popis..."
          v-on:blur="updateInternalNote"
        ></textarea>

        <div
          class="mb-4 flex space-x-2 rounded-md border border-gray-200 px-3 py-2 dark:border-zinc-800"
        >
          <p
            class="inline-block rounded-md p-2 py-1 text-gray-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
            @click="switchTab('data')"
          >
            Podrobnosti
          </p>
          <p
            class="inline-block rounded-md p-2 py-1 text-gray-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
            @click="switchTab('expenses')"
          >
            Výdaje
          </p>

          <p
            class="inline-block rounded-md p-2 py-1 text-gray-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
            @click="switchTab('counter_bank_accounts')"
          >
            Účty
          </p>
        </div>

        <organisation-data
          :organisation="organisation"
          v-if="tab === 'data'"
        ></organisation-data>

        <organisation-expenses
          v-if="tab === 'expenses'"
          :organisation="organisation"
        ></organisation-expenses>

        <organisation-counter-bank-accounts
          v-if="tab === 'counter_bank_accounts'"
          :organisation="organisation"
        ></organisation-counter-bank-accounts>
      </div>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import OrganisationCounterBankAccounts from "~/components/organisation/OrganisationCounterBankAccounts.vue";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const tab = ref("data");

const loaded = ref(false);

const organisation = ref(null);
const input_name = ref("");
const input_internal_note = ref("");

onMounted(() => {
  let route = useRoute();
  if (route.query.tab) {
    switchTab(route.query.tab);
  }

  fetchData();
});

function switchTab(tab_name) {
  const router = useRouter();
  tab.value = tab_name;
  router.replace({ query: { tab: tab_name } });
}

async function fetchData() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("organisation", () =>
    client(
      "/api/" +
      route.params.workspace +
      "/organisations/" +
      route.params.organisation,
      {
        method: "GET"
      }
    )
  );

  organisation.value = data.value;
  input_name.value = data.value.name;
  input_internal_note.value = data.value.internal_note;

  useHead({
    title: organisation.value.name
  });

  loaded.value = true;
}

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

async function updateName() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("organisation", () =>
    client(
      "/api/" +
      route.params.workspace +
      "/organisations/" +
      route.params.organisation,
      {
        method: "PATCH",
        body: {
          name: input_name.value
        }
      }
    )
  );
}

async function updateInternalNote() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("organisation", () =>
    client(
      "/api/" +
      route.params.workspace +
      "/organisations/" +
      route.params.organisation,
      {
        method: "PATCH",
        body: {
          internal_note: input_internal_note.value
        }
      }
    )
  );
}
</script>