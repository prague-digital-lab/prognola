<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-40">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/20" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div
          class="flex min-h-full items-start justify-center p-4 pt-[70px] text-center"
        >
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              class="w-full max-w-2xl transform select-none rounded-2xl bg-white p-6 text-left align-middle shadow transition-all dark:border dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400"
            >
              <DialogTitle
                as="p"
                class="text-base leading-6 text-gray-700 dark:text-zinc-200"
              >
                Nový výdaj
              </DialogTitle>

              <form v-on:submit.prevent="createExpense">
                <div class="flex justify-between">
                  <input
                    class="mb-2 flex-1 border-none px-0 py-2 font-medium focus:ring-0 dark:bg-transparent dark:text-zinc-200"
                    placeholder="Název výdaje"
                    type="text"
                    required
                    v-model="expense_name"
                  />
                  <div>
                    <input
                      class="mb-2 border-none px-0 py-2 text-end font-medium focus:ring-0 dark:bg-transparent dark:text-zinc-200"
                      type="number"
                      step="0.01"
                      v-model="price"
                    />

                    Kč
                  </div>
                </div>

                <div class="flex justify-between">
                  <div class="flex items-center">
                    <modal-organisation-picker
                      v-model="organisation_uuid"
                    ></modal-organisation-picker>

                    <modal-paid-at-input
                      v-model="paid_at"
                    ></modal-paid-at-input>
                  </div>
                  <div>
                    <input
                      id="create_more"
                      type="checkbox"
                      v-model="create_more"
                      class="me-2 rounded-md"
                    />
                    <label for="create_more" class="me-4 text-sm text-gray-500"
                      >Vytvořit více</label
                    >

                    <button
                      type="submit"
                      class="inline-block rounded-md bg-black px-3 py-2 text-base text-white hover:bg-gray-800"
                    >
                      Vytvořit výdaj
                    </button>
                  </div>
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref } from "vue";
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { DateTime } from "luxon";
import ModalOrganisationPicker from "~/components/ui/modals/expense_create_modal/ModalOrganisationPicker.vue";
import ModalPaidAtInput from "~/components/ui/modals/expense_create_modal/ModalPaidAtInput.vue";
import { addExpense } from "~/lib/dexie/repository/expense_repository.js";

const isOpen = ref(false);

const props = defineProps(["default_organisation_uuid", "default_paid_at"]);

// Modal data
const expense_name = ref("");
const organisation_uuid = ref(null);
const price = ref(0);
const paid_at = ref();

const create_more = ref(false);

function openModal() {
  isOpen.value = true;
}

function closeModal() {
  isOpen.value = false;
}

defineExpose({
  openModal,
  closeModal,
});

const emit = defineEmits(["expense-created"]);

onMounted(() => {
  if (props.default_organisation_uuid !== null) {
    organisation_uuid.value = props.default_organisation_uuid;
  }

  if (props.default_paid_at !== null) {
    paid_at.value = props.default_paid_at;
  }

  price.value = 0;
});

watch(
  () => props.default_paid_at,
  (newX) => {
    paid_at.value = props.default_paid_at;
  },
);

function formatDate(date) {
  let formatted = DateTime.fromISO(date);

  return formatted.toFormat("d.M.yyyy");
}

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

async function createExpense() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("expense", () =>
    client("/api/" + route.params.workspace + "/expenses", {
      method: "POST",
      body: {
        description: expense_name.value,
        price: price.value,
        paid_at: paid_at.value,
        organisation: organisation_uuid.value,
        payment_status: "plan",
      },
    }),
  );

  await addExpense(data.value);

  emit("expense-created");

  if (create_more.value === false) {
    expense_name.value = "";
    price.value = 0;
    organisation_uuid.value = null;

    closeModal();
  }
}
</script>
