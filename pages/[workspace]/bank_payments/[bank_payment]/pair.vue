<template>
  <Head>
    <Title>Párování platby</Title>
  </Head>

  <div v-if="loaded">
    <div
      class="mb-4 h-auto divide-x divide-slate-100 md:flex md:justify-between"
    >
      <div class="me-5 w-full">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Párování platby {{ bank_payment.id }}
        </h4>

        <button-secondary @click="navigateToBankPayment"
          >Zobrazit stránku s platbou
        </button-secondary>
      </div>
    </div>

    <div>
      <div class="mb-4 overflow-hidden bg-white shadow sm:rounded-lg">
        <!--        <div class="px-4 py-6 sm:px-6">-->
        <!--          <h3 class="text-base font-semibold leading-7 text-gray-900">Informace o platbě</h3>-->
        <!--        </div>-->
        <div class="border-t border-gray-100">
          <dl class="divide-y divide-gray-100">
            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">Částka</dt>
              <dd
                class="mt-1 text-base font-bold leading-6 text-red-700 sm:col-span-2 sm:mt-0"
              >
                {{ formatPrice(bank_payment.amount) }} Kč
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">Účet</dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ bank_payment.bank_account.name }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">Popis platby</dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ bank_payment.description }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">
                Zpráva pro příjemce
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ bank_payment.sender_comment }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">Externí ID</dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ bank_payment.external_id }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">
                Variabilní symbol
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ bank_payment.variable_symbol }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">Datum platby</dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ formatDate(bank_payment.issued_at) }}
              </dd>
            </div>

            <div
              class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
              v-if="bank_payment.counter_account_number"
            >
              <dt class="text-base font-medium text-gray-900">
                Číslo protiúčtu
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ bank_payment.counter_account_number }}/{{
                  bank_payment.counter_bank_number
                }}
              </dd>
            </div>

            <div
              class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
              v-if="bank_payment.paired_at"
            >
              <dt class="text-base font-medium text-gray-900">Spárováno</dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ formatDate(bank_payment.paired_at) }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <div class="flex items-center justify-between">
        <h2 class="mb-2 mt-4 text-base font-semibold text-gray-700">
          Vyhledávání výdaje ke spárování
        </h2>

        <button-secondary @click="createNewExpense">
          Vytvořit nový výdaj
        </button-secondary>
      </div>

      <div
        class="mb-4 overflow-hidden border border-gray-100 bg-white py-2 shadow sm:rounded-lg sm:px-6"
      >
        <input
          placeholder="Název výdaje"
          class="me-4 rounded"
          v-model="name_query"
        />
        <input
          placeholder="Částka"
          class="me-4 rounded"
          v-model="price_query"
        />

        <a @click="searchExpenses" class="cursor-pointer">Vyhledat výdaje</a>
      </div>

      <div
        class="mb-4 divide-y divide-gray-200 rounded border border-gray-200"
        v-if="expenses_loaded"
      >
        <expense-row-pair
          v-for="expense in expenses"
          :expense="expense"
          @click="pairExpense(expense)"
        ></expense-row-pair>

        <div
          v-if="expenses.length === 0"
          class="flex h-[100px] w-full items-center justify-center"
        >
          <p class="text-gray-600">Žádné odpovídající výdaje ke spárování.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>

<script>
import { formatDate } from "compatx";

export default {
  data() {
    return {
      loaded: false,

      bank_payment: null,

      price_query: "",
      name_query: "",

      expenses: [],
      expenses_loaded: false,
    };
  },

  mounted() {
    this.fetchBankPayment();
  },

  methods: {
    formatDate,

    async fetchBankPayment() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_payment", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/bank_payments/" +
            route.params.bank_payment,
          {
            method: "GET",
          },
        ),
      );

      this.bank_payment = data.value;
      this.price_query = this.bank_payment.amount * -1;
      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async searchExpenses() {
      const client = useSanctumClient();
      const route = useRoute();

      let params = {
        is_paired: false,
      };

      if (this.name_query) {
        params.query = this.name_query;
      }

      if (this.price_query) {
        params.price = this.price_query;
      }

      const { data } = await useAsyncData("bank_payment_pairing_expenses", () =>
        client("/api/" + route.params.workspace + "/expenses", {
          method: "GET",
          params: params,
        }),
      );

      this.expenses = data.value.data;
      this.expenses_loaded = true;
    },

    async createNewExpense() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("expense", () =>
        client("/api/" + route.params.workspace + "/expenses", {
          method: "POST",
          body: {
            description: this.bank_payment.description,
            price: this.bank_payment.amount * -1,
            payment_status: "paid",
            received_at: this.bank_payment.issued_at,
            paid_at: this.bank_payment.issued_at,
          },
        }),
      );

      let id = data.value.uuid;

      const pairing_data = await useAsyncData("expense", () =>
        client("/api/expenses/" + uuid + "/bank_payments", {
          method: "POST",
          body: {
            bank_payment_id: this.bank_payment.id,
          },
        }),
      );

      await navigateTo("/expenses/" + id);
    },

    async pairExpense(expense) {
      const client = useSanctumClient();
      const route = useRoute();

      const pairing_data = await useAsyncData("expense", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/expenses/" +
            expense.uuid +
            "/bank_payments",
          {
            method: "POST",
            body: {
              bank_payment: this.bank_payment.uuid,
            },
          },
        ),
      );

      await navigateTo(
        "/" + route.params.workspace + "/inbox/payments_to_pair",
      );
    },

    async navigateToBankPayment() {
      const route = useRoute();

      await navigateTo(
        "/" +
          route.params.workspace +
          "/bank_payments/" +
          this.bank_payment.uuid,
      );
    },
  },
};
</script>
