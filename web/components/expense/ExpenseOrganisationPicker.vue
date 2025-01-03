<template>
  <div class="w-full max-w-sm">
    <Popover v-slot="{ open }" class="relative">
      <div class="flex">
        <PopoverButton
          :class="
            open
              ? 'bg-gray-200 dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-800'
              : ''
          "
          class="text-xs mb-7 me-2 rounded py-1 pe-3 ps-1 text-gray-800 hover:bg-gray-100 active:bg-gray-200 dark:hover:bg-zinc-900"
        >
          <p class="flex text-gray-800 dark:text-zinc-400">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="me-3 inline-block size-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"
              />
            </svg>

            <span
              v-if="selected_organisation"
              class="text-left text-gray-700 dark:text-zinc-400"
            >
              {{ selected_organisation.name }}
            </span>
            <span v-else class="text-gray-500 dark:text-zinc-500">
              zvolit organizaci
            </span>
          </p>
        </PopoverButton>

        <nuxt-link
          :href="
            '/' +
            route.params.workspace +
            '/organisations/' +
            selected_organisation.uuid
          "
          class="text-xs mb-7 flex cursor-default items-center rounded px-2 py-1 text-gray-500 duration-100 hover:bg-gray-100 active:bg-gray-200/80 dark:hover:bg-zinc-800"
          v-if="selected_organisation"
        >
          <chevron-double-right-icon class="h-4 w-4 text-gray-500" />
        </nuxt-link>
      </div>

      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-1 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-1 opacity-0"
      >
        <PopoverPanel
          v-slot="{ close }"
          class="absolute left-[-260px] top-0 z-10 w-[250px]"
        >
          <div
            class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black/5 dark:border dark:border-zinc-700"
          >
            <div class="relative bg-white p-2 dark:bg-zinc-950">
              <input
                class="mb-2 w-full rounded border-gray-200 py-1 text-base text-gray-700 focus:border-gray-200 focus:ring-0 dark:border-zinc-800 dark:bg-black dark:text-zinc-300"
                type="text"
                placeholder="Název organizace..."
                v-model="organisation_name_filter"
              />

              <div class="max-h-[50vh] overflow-auto">
                <p
                  class="rounded-md px-2 py-1 text-base text-gray-700 hover:bg-gray-hover active:bg-gray-100 dark:text-zinc-400 dark:hover:bg-zinc-900"
                  v-for="organisation in filtered_organisations"
                  @click="selectOrganisation(organisation, close)"
                >
                  {{ organisation.name }}
                </p>

                <p
                  class="cursor-pointer rounded-md px-2 py-1 text-base text-gray-500 hover:bg-gray-hover active:bg-gray-100"
                  v-if="filtered_organisations.length === 0"
                  @click="createOrganisation(close)"
                >
                  + vytvořit firmu "{{ organisation_name_filter }}"
                </p>
              </div>
            </div>
          </div>
        </PopoverPanel>
      </transition>
    </Popover>
  </div>
</template>

<script setup>
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { ChevronDoubleRightIcon } from "@heroicons/vue/24/solid/index.js";
import { updateExpense } from "~/lib/dexie/repository/expense_repository.js";
import {
  getOrganisation,
  getOrganisations,
} from "~/lib/dexie/repository/organisation_repository.js";

const props = defineProps(["expense"]);

const organisation_name_filter = ref("");

const organisations = ref([]);
const selected_organisation = ref(null);

const route = useRoute();
// const open = ref(false);

onMounted(async () => {
  if (props.expense.organisation) {
    selected_organisation.value = await getOrganisation(
      props.expense.organisation,
    );
  }

  await loadOrganisations();
});

async function selectOrganisation(organisation, close) {
  selected_organisation.value = organisation;

  const client = useSanctumClient();
  const route = useRoute();

  let expense = props.expense;
  expense.organisation = organisation.uuid;
  await updateExpense(expense.uuid, expense);

  const { data } = await useAsyncData("expense", () =>
    client(
      "/api/" + route.params.workspace + "/expenses/" + props.expense.uuid,
      {
        method: "PATCH",
        body: {
          organisation: organisation.uuid,
        },
      },
    ),
  );

  close();
}

async function loadOrganisations() {
  organisations.value = await getOrganisations();
}

async function createOrganisation(close) {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("organisation", () =>
    client("/api/" + route.params.workspace + "/organisations", {
      method: "POST",
      body: {
        name: organisation_name_filter.value,
      },
    }),
  );

  await selectOrganisation(data.value, close);
}

const filtered_organisations = computed(() => {
  return organisations.value.filter((organisation) => {
    return (
      !organisation_name_filter.value ||
      organisation.name
        .toLowerCase()
        .indexOf(organisation_name_filter.value.toLowerCase()) > -1
    );
  });
});
</script>
