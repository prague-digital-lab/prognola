<template>
  <div class="w-56">
    <Menu as="div" class="relative inline-block">
      <div>
        <MenuButton class="">
          <p
            class="mb-1 rounded px-1 py-1 text-sm text-gray-800 hover:bg-gray-100"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="currentColor"
              class="me-2 inline-block size-5 text-gray-600"
            >
              <path
                fill-rule="evenodd"
                d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"
                clip-rule="evenodd"
              />
            </svg>

            {{ formatPrice(payment.amount) }} Kč
          </p>
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
  </div>
</template>

<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

defineProps(["payment"]);
const emit = defineEmits(["income-updated"]);

async function navigateToPayment(payment) {
  const route = useRoute();
  await navigateTo(
    "/" + route.params.workspace + "/bank_payments/" + payment.uuid,
  );
}

async function unpairPayment(payment) {
  const route = useRoute();
  const client = useSanctumClient();

  const { data } = await useAsyncData("income", () =>
    client(
      "/api/" +
        route.params.workspace +
        "/incomes/" +
        route.params.income +
        "/bank_payments",
      {
        method: "DELETE",
        body: {
          bank_payment: payment.uuid,
        },
      },
    ),
  );

  emit("income-updated");
}

function formatPrice(value) {
  let val = (value / 1).toFixed(2).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}
</script>
