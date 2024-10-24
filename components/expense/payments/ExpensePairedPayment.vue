<template>
  <Menu as="div" class="relative">
    <div>
      <MenuButton class="w-full">
        <div
          class="mb-1 flex justify-between rounded px-1 py-1 pe-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-zinc-400 dark:hover:bg-zinc-900"
        >
          <div class="flex items-center">
            <minus-circle-icon v-if="payment.type === 'expense'" class="h-4 w-4 me-1 inline-block dark:text-red-500"></minus-circle-icon>
            <plus-circle-icon v-if="payment.type === 'income'" class="h-4 w-4 me-1 inline-block dark:text-blue-500"></plus-circle-icon>

            {{ formatPrice(payment.amount) }} Kč
          </div>

          <div class="text-sm font-light text-gray-500">
            {{ formatDate(payment.issued_at) }}
          </div>
        </div>
      </MenuButton>
    </div>

    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <MenuItems
        class="absolute right-0 top-6 z-40 mt-2 w-56 divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
      >
        <div class="px-1 py-1">
          <MenuItem v-slot="{ active }">
            <button
              :class="[
                active ? 'bg-violet-500 text-white' : 'text-gray-900',
                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
              ]"
              @click="navigateToPayment(payment)"
            >
              Přejít na stránku platby
            </button>
          </MenuItem>
          <MenuItem v-slot="{ active }">
            <button
              :class="[
                active ? 'bg-violet-500 text-white' : 'text-gray-900',
                'group flex w-full items-center rounded-md px-2 py-2 text-sm',
              ]"
              @click="unpairPayment(payment)"
            >
              Zrušit párování platby
            </button>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import {MinusCircleIcon, PlusCircleIcon} from "@heroicons/vue/20/solid";
import { DateTime } from "luxon";

defineProps(["payment"]);
const emit = defineEmits(["expense-updated"]);

async function navigateToPayment(payment) {
  const route = useRoute();
  await navigateTo(
    "/" + route.params.workspace + "/bank_payments/" + payment.uuid,
  );
}

async function unpairPayment(payment) {
  const route = useRoute();
  const client = useSanctumClient();

  const { data } = await useAsyncData("expense", () =>
    client(
      "/api/" +
        route.params.workspace +
        "/expenses/" +
        route.params.expense +
        "/bank_payments",
      {
        method: "DELETE",
        body: {
          bank_payment: payment.uuid,
        },
      },
    ),
  );

  emit("expense-updated");
}

function formatPrice(value) {
  let val = (value / 1).toFixed(2).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function formatDate(date) {
  let formatted = DateTime.fromISO(date);

  return formatted.toFormat("d.M.yyyy");
}
</script>
