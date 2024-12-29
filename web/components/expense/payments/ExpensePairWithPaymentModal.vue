<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-50">
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
          class="flex min-h-full items-center justify-center p-4 text-center"
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
              class="w-full max-w-4xl transform overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 text-left align-middle transition-all"
            >
              <DialogTitle as="h3" class="font-medium leading-6 text-gray-900">
                Přidání existující platby
              </DialogTitle>

              <div>
                <div>
                  <div class="flex items-center justify-between">
                    <h2 class="mb-4 mt-4 text-base text-gray-700">
                      Vyhledejte platbu, která souvisí s výdajem
                      {{ props.expense.description }}.
                    </h2>
                  </div>

                  <div
                    class="mb-4 overflow-hidden border border-gray-200 bg-white py-2 sm:rounded-lg sm:px-6"
                  >
                    <form v-on:submit.prevent="searchPayments">
                      <input
                        placeholder="Název platby"
                        class="me-4 rounded border border-gray-200 py-1 text-base"
                        v-model="name_query"
                      />
                      <input
                        placeholder="Částka"
                        class="me-4 rounded border border-gray-200 py-1 text-base"
                        v-model="price_query"
                      />

                      <button
                        type="submit"
                        class="cursor-pointer rounded-md bg-black px-3 py-2 text-base text-white duration-100 hover:bg-gray-700"
                      >
                        Vyhledat platby
                      </button>
                    </form>
                  </div>

                  <div
                    class="mb-4 divide-y divide-gray-200 rounded border border-gray-200"
                    v-if="bank_payments_loaded"
                  >
                    <bank-payment-pair-row
                      v-for="bank_payment in bank_payments"
                      :bank_payment="bank_payment"
                      @click="pairBankPayment(bank_payment)"
                    ></bank-payment-pair-row>

                    <div
                      v-if="bank_payments.length === 0"
                      class="flex h-[100px] w-full items-center justify-center"
                    >
                      <p class="text-base text-gray-600">
                        Žádné odpovídající platby. Zkuste upravit zadání.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
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

const isOpen = ref(false);

const route = useRoute();
const loaded = ref(false);

const expense = ref(null);

const price_query = ref("");
const name_query = ref("");

const bank_payments = ref([]);
const bank_payments_loaded = ref(false);

function closeModal() {
  isOpen.value = false;
}

function openModal() {
  isOpen.value = true;

  loaded.value = true;
  price_query.value = props.expense.price;

  searchPayments();
}

onMounted(() => {
  // fetchExpense();
});

defineExpose({
  openModal,
  closeModal,
});

const props = defineProps(["expense"]);

const emit = defineEmits(["expense-updated"]);

async function pairBankPayment(bank_payment) {
  const client = useSanctumClient();
  const route = useRoute();

  const pairing_data = await useAsyncData("expense", () =>
    client(
      "/api/" +
        route.params.workspace +
        "/expenses/" +
        route.params.expense +
        "/bank_payments",
      {
        method: "POST",
        body: {
          bank_payment: bank_payment.uuid,
        },
      },
    ),
  );

  closeModal();
  emit("expense-updated");
}

function formatDate(date) {
  let formatted = DateTime.fromISO(date);

  return formatted.toFormat("d.M.yyyy");
}

async function searchPayments() {
  const client = useSanctumClient();

  let params = {
    is_paired: false,
  };

  if (name_query.value) {
    params.query = name_query.value;
  }

  if (price_query.value) {
    params.amount = price_query.value;
  }

  const { data } = await useAsyncData("bank_payment_pairing_expenses", () =>
    client("/api/" + route.params.workspace + "/bank_payments", {
      method: "GET",
      params: params,
    }),
  );

  bank_payments.value = data.value;
  bank_payments_loaded.value = true;
}

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}
</script>
