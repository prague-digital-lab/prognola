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
              class="w-full max-w-md transform overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 text-left align-middle transition-all"
            >
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900"
              >
                Párování příjmu {{ income.name }}
              </DialogTitle>

              <div v-if="loaded">
                <div>
                  <div class="flex items-center justify-between">
                    <h2 class="mb-2 mt-4 text-base text-gray-700">
                      Vyhledávání plateb ke spárování
                    </h2>
                  </div>

                  <div
                    class="mb-4 overflow-hidden border border-gray-200 bg-white py-2 sm:rounded-lg sm:px-6"
                  >
                    <input
                      placeholder="Název platby"
                      class="me-4 rounded border border-gray-200 text-base"
                      v-model="name_query"
                    />
                    <input
                      placeholder="Částka"
                      class="me-4 rounded border border-gray-200 text-base"
                      v-model="price_query"
                    />

                    <a @click="searchPayments" class="cursor-pointer"
                      >Vyhledat platby</a
                    >
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
                      <p class="text-gray-600">
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

const isOpen = ref(false);

function closeModal() {
  isOpen.value = false;
}

function openModal() {
  isOpen.value = true;
}

defineExpose({
  openModal,
  closeModal,
});

const emit = defineEmits(["incomeUpdated"]);

async function pairBankPayment(bank_payment) {
  const client = useSanctumClient();
  const route = useRoute();

  const pairing_data = await useAsyncData("income", () =>
    client(
      "/api/" +
        route.params.workspace +
        "/incomes/" +
        route.params.income +
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
  emit("incomeUpdated");
}
</script>

<script>
import { formatDate } from "compatx";

export default {
  data() {
    return {
      route: null,
      loaded: false,

      expense: null,

      price_query: "",
      name_query: "",

      bank_payments: [],
      bank_payments_loaded: false,
    };
  },

  mounted() {
    this.route = useRoute();
    this.fetchIncome();
  },

  methods: {
    formatDate,

    async fetchIncome() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/incomes/" +
            this.route.params.income,
          {
            method: "GET",
          },
        ),
      );

      this.income = data.value;
      this.price_query = this.income.amount;
      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async searchPayments() {
      const client = useSanctumClient();
      const route = useRoute();

      let params = {
        is_paired: false,
      };

      if (this.name_query) {
        params.query = this.name_query;
      }

      if (this.price_query) {
        params.amount = this.price_query;
      }

      const { data } = await useAsyncData("bank_payment_pairing_expenses", () =>
        client("/api/" + route.params.workspace + "/bank_payments", {
          method: "GET",
          params: params,
        }),
      );

      this.bank_payments = data.value;
      this.bank_payments_loaded = true;
    },
  },
};
</script>
