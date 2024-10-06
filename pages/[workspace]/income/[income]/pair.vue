<template>
  <Head>
    <Title>Párování plateb</Title>
  </Head>

  <div v-if="loaded">
    <div class="mb-4 h-auto md:flex md:justify-between">
      <div class="me-5 w-full">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Párování příjmu {{ income.name }}
        </h4>
      </div>
    </div>

    <div>
      <h2 class="mb-2 mt-4 text-base text-gray-700">Příjem</h2>

      <div
        class="mb-4 overflow-hidden rounded-md border border-gray-200 bg-white"
      >
        <!--        <div class="px-4 py-6 sm:px-6">-->
        <!--          <h3 class="text-base font-semibold leading-7 text-gray-900">Informace o platbě</h3>-->
        <!--        </div>-->
        <div class="">
          <dl class="divide-y divide-gray-100">
            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">Částka</dt>
              <dd
                class="mt-1 text-base font-bold leading-6 sm:col-span-2 sm:mt-0"
              >
                {{ formatPrice(income.amount) }} Kč
              </dd>
            </div>

            <!--            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">-->
            <!--              <dt class="text-base font-medium text-gray-900">Účet</dt>-->
            <!--              <dd-->
            <!--                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"-->
            <!--              >-->
            <!--                {{ bank_payment.bank_account.name }}-->
            <!--              </dd>-->
            <!--            </div>-->

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">Popis platby</dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ income.description }}
              </dd>
            </div>

            <!--            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">-->
            <!--              <dt class="text-base font-medium text-gray-900">-->
            <!--                Zpráva pro příjemce-->
            <!--              </dt>-->
            <!--              <dd-->
            <!--                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"-->
            <!--              >-->
            <!--                {{ uncim.sender_comment }}-->
            <!--              </dd>-->
            <!--            </div>-->

            <!--            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">-->
            <!--              <dt class="text-base font-medium text-gray-900">Externí ID</dt>-->
            <!--              <dd-->
            <!--                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"-->
            <!--              >-->
            <!--                {{ bank_payment.external_id }}-->
            <!--              </dd>-->
            <!--            </div>-->

            <!--            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">-->
            <!--              <dt class="text-base font-medium text-gray-900">-->
            <!--                Variabilní symbol-->
            <!--              </dt>-->
            <!--              <dd-->
            <!--                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"-->
            <!--              >-->
            <!--                {{ bank_payment.variable_symbol }}-->
            <!--              </dd>-->
            <!--            </div>-->

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-base font-medium text-gray-900">Datum platby</dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"
              >
                {{ formatDate(income.paid_at) }}
              </dd>
            </div>

            <!--            <div-->
            <!--              class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"-->
            <!--              v-if="bank_payment.counter_account_number"-->
            <!--            >-->
            <!--              <dt class="text-base font-medium text-gray-900">-->
            <!--                Číslo protiúčtu-->
            <!--              </dt>-->
            <!--              <dd-->
            <!--                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"-->
            <!--              >-->
            <!--                {{ bank_payment.counter_account_number }}/{{-->
            <!--                  bank_payment.counter_bank_number-->
            <!--                }}-->
            <!--              </dd>-->
            <!--            </div>-->

            <!--            <div-->
            <!--              class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"-->
            <!--              v-if="bank_payment.paired_at"-->
            <!--            >-->
            <!--              <dt class="text-base font-medium text-gray-900">Spárováno</dt>-->
            <!--              <dd-->
            <!--                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0"-->
            <!--              >-->
            <!--                {{ formatDate(bank_payment.paired_at) }}-->
            <!--              </dd>-->
            <!--            </div>-->
          </dl>
        </div>
      </div>

      <div class="flex items-center justify-between">
        <h2 class="mb-2 mt-4 text-base text-gray-700">
          Vyhledávání plateb ke spárování
        </h2>

        <a
          type="button"
          class="rounded-md bg-black px-2 py-1 text-base font-semibold text-gray-300 duration-100 hover:bg-gray-700"
          @click="createNewPayment"
        >
          Vytvořit novou platbu
        </a>
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

        <a @click="searchPayments" class="cursor-pointer">Vyhledat platby</a>
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
</template>

<script setup>
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

    /**
     * TODO: fix
     * @returns {Promise<void>}
     */
    async createNewPayment() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("bank_payment", () =>
        client("/api/expenses", {
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

      let id = data.value.id;

      const pairing_data = await useAsyncData("expense", () =>
        client("/api/expenses/" + id + "/bank_payments", {
          method: "POST",
          body: {
            bank_payment_id: this.bank_payment.id,
          },
        }),
      );

      await navigateTo("/expenses/" + id);
    },

    async pairBankPayment(bank_payment) {
      const client = useSanctumClient();
      const route = useRoute();

      const pairing_data = await useAsyncData("income", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/incomes/" +
            this.income.uuid +
            "/bank_payments",
          {
            method: "POST",
            body: {
              bank_payment: bank_payment.uuid,
            },
          },
        ),
      );

      await navigateTo(
        "/" + route.params.workspace + "/income/" + this.income.uuid,
      );
    },
  },
};
</script>
