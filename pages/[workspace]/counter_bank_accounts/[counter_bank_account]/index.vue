<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <heading
            >{{ counter_bank_account.account_number }}/{{
              counter_bank_account.bank_number
            }}
          </heading>
        </div>
      </template>
      <template v-slot:subtitle>
        <p class="text-gray-700 dark:text-zinc-400">Protiúčet v bance</p>
      </template>
    </page-content-header>

    <div
      class="g-white mb-4 w-1/2 border border-gray-200 sm:rounded-lg dark:border-zinc-800 dark:bg-zinc-900"
    >
      <div class="">
        <dl class="divide-y divide-gray-100 dark:divide-zinc-800">
          <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt
              class="flex items-center text-base font-medium text-gray-900 dark:text-zinc-300"
            >
              Organizace
            </dt>
            <dd class="mt-1 flex items-center sm:col-span-2 sm:mt-0">
              <modal-organisation-picker
                v-model="organisation_uuid"
              ></modal-organisation-picker>
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import Heading from "~/components/ui/Heading.vue";
import "./index.vue";
import ModalOrganisationPicker from "~/components/ui/modals/expense_create_modal/ModalOrganisationPicker.vue";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const loaded = ref(false);
const counter_bank_account = ref(null);
const organisation_uuid = ref();

onMounted(() => {
  fetchData();
});

async function fetchData() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("counter_bank_accounts", () =>
    client(
      "/api/" +
        route.params.workspace +
        "/counter_bank_accounts/" +
        route.params.counter_bank_account,
      {
        method: "GET",
      },
    ),
  );

  counter_bank_account.value = data.value;
  organisation_uuid.value = data.value.uuid;

  loaded.value = true;
}
</script>
