<template>
  <div class="">
    <Popover class="relative">
      <PopoverButton>
        <p
          class="w-[90px] ps-2 text-zinc-500 dark:text-zinc-400"
          @contextmenu="openPaidAtContextMenu"
          v-if="expense.paid_at"
        >
          {{ formatDate(expense.paid_at) }}
        </p>
      </PopoverButton>

      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-1 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-1 opacity-0"
      >
        <PopoverPanel
          v-on:click.prevent
          class="absolute left-[-245px] top-[-10px] w-[250px] cursor-default"
        >
          <div
            class="overflow-hidden rounded-lg bg-white px-3 py-4 shadow-lg ring-1 ring-black/5 dark:border dark:border-zinc-700"
          >
            Datum platby

            <input
              class="mb-2 w-full rounded"
              type="date"
              autofocus
              v-on:blur="updateDate"
              v-model="paid_at"
            />
          </div>
        </PopoverPanel>
      </transition>
    </Popover>
  </div>
</template>

<script setup>
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { DateTime } from "luxon";
import { updateExpenseFromLocalObject } from "~/lib/dexie/repository/expense_repository.js";

const props = defineProps(["expense"]);
const emit = defineEmits(["expense-updated"]);

const paid_at = ref(null);

onMounted(async () => {
  paid_at.value = DateTime.fromJSDate(props.expense.paid_at).toFormat(
    "yyyy-MM-dd",
  );
});

function openPaidAtContextMenu(e) {
  e.target.click();

  e.preventDefault();
}

function formatDate(date) {
  if (paid_at.value === null) {
    return null;
  }

  let formatted = DateTime.fromJSDate(date);

  return formatted.toFormat("d.M.yyyy");
}

async function updateDate() {
  const client = useSanctumClient();
  const route = useRoute();

  let expense = props.expense;

  console.log("expense", expense);
  expense.paid_at = DateTime.fromFormat(paid_at.value, "yyyy-MM-dd").toJSDate();

  await updateExpenseFromLocalObject(expense.uuid, expense);

  emit("expense-updated");

  const data = await client(
    "/api/" + route.params.workspace + "/expenses/" + expense.uuid,
    {
      method: "PATCH",
      body: {
        paid_at: paid_at.value,
      },
    },
  );
}
</script>
