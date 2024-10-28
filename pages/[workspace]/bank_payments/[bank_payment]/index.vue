<template>
  <Head>
    <Title>Detail platby</Title>
  </Head>

  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <heading>Detail platby</heading>
      </template>

      <template v-slot:subtitle>
        <p
          class="text-gray-700 dark:text-zinc-400"
          v-if="bank_payment.amount > 0"
        >
          Příchozí platba
        </p>
        <p class="text-gray-700 dark:text-zinc-400" v-else>Odchozí platba</p>
      </template>
    </page-content-header>

    <div class="grid grid-cols-2 gap-3">
      <div
        class="b g-white mb-4 overflow-hidden border border-gray-200 sm:rounded-lg dark:border-zinc-800 dark:bg-zinc-900"
      >
        <div class="">
          <dl class="divide-y divide-gray-100 dark:divide-zinc-800">
            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Částka
              </dt>
              <dd
                class="mt-1 flex items-center text-base font-bold leading-6 sm:col-span-2 sm:mt-0"
              >
                <bank-payment-amount
                  :bank_payment="bank_payment"
                ></bank-payment-amount>
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Účet
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0 dark:text-zinc-400"
              >
                <badge-bank-account :bank_account="bank_payment.bank_account" />
              </dd>
            </div>

            <div
              class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
              v-if="bank_payment.counter_account_number"
            >
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Protiúčet
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0 dark:text-zinc-400"
              >
                <badge-counter-bank-account
                  v-if="bank_payment.counter_bank_account"
                  :counter_bank_account="bank_payment.counter_bank_account"
                ></badge-counter-bank-account>

                <span v-else
                  >{{ bank_payment.counter_account_number }}/{{
                    bank_payment.counter_bank_number
                  }}</span
                >
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Popis platby
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0 dark:text-zinc-400"
              >
                {{ bank_payment.description }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Zpráva pro příjemce
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0 dark:text-zinc-400"
              >
                {{ bank_payment.sender_comment }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Externí ID
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0 dark:text-zinc-400"
              >
                {{ bank_payment.external_id }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Variabilní symbol
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0 dark:text-zinc-400"
              >
                {{ bank_payment.variable_symbol }}
              </dd>
            </div>

            <div class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Datum platby
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0 dark:text-zinc-400"
              >
                {{ formatDate(bank_payment.issued_at) }}
              </dd>
            </div>

            <div
              class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
              v-if="bank_payment.paired_at"
            >
              <dt
                class="text-base font-medium text-gray-900 dark:text-zinc-300"
              >
                Spárováno
              </dt>
              <dd
                class="mt-1 text-base leading-6 text-gray-700 sm:col-span-2 sm:mt-0 dark:text-zinc-400"
              >
                {{ formatDate(bank_payment.paired_at) }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <div
        class="mb-4 overflow-hidden border border-gray-200 bg-white p-6 sm:rounded-lg dark:border-zinc-800 dark:bg-zinc-900"
      >
        <div
          v-if="
            bank_payment.incomes.length > 0 || bank_payment.expenses.length > 0
          "
        >
          <div class="mb-4 flex items-center justify-between">
            <p class="font-medium text-gray-600 dark:text-zinc-200">
              Spárované příjmy a výdaje
            </p>

            <div>
              <button-secondary @click="navigateToPair"
                >Spárovat příjem/výdaj
              </button-secondary>
            </div>
          </div>

          <div
            v-for="income in bank_payment.incomes"
            class="mt-2 text-gray-600 dark:text-zinc-400"
          >
            Příjem
            <badge-income :income="income" class="mx-2" />
            {{ income.pivot.amount }} Kč
          </div>

          <div
            v-for="expense in bank_payment.expenses"
            class="mt-2 text-gray-600 dark:text-zinc-400"
          >
            Výdaj
            <badge-expense :expense="expense" class="mx-2" />
            {{ expense.pivot.amount }} Kč
          </div>
        </div>
        <div v-else>
          <button-secondary @click="navigateToPair"
            >Spárovat příjem/výdaj
          </button-secondary>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BadgeIncome from "~/components/badges/BadgeIncome.vue";
import BadgeBankAccount from "~/components/badges/BadgeBankAccount.vue";
import BadgeExpense from "~/components/badges/BadgeExpense.vue";
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import Heading from "~/components/ui/Heading.vue";
import BankPaymentAmount from "~/components/bank_payment/BankPaymentAmount.vue";
import BadgeCounterBankAccount from "~/components/badges/BadgeCounterBankAccount.vue";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>

<script>
import { DateTime } from "luxon";

export default {
  data() {
    return {
      route: null,
      loaded: false,

      bank_payment: null,

      price_query: "",
      name_query: "",

      expenses: [],
      expenses_loaded: false,
    };
  },

  mounted() {
    this.route = useRoute();
    this.fetchBankPayment();
  },

  methods: {
    formatDate(date) {
      let formatted = DateTime.fromISO(date);

      return formatted.toFormat("d.M.yyyy");
    },

    async navigateToPair() {
      const route = useRoute();

      await navigateTo(
        "/" +
          route.params.workspace +
          "/bank_payments/" +
          this.bank_payment.uuid +
          "/pair",
      );
    },

    async fetchBankPayment() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("bank_payment", () =>
        client(
          "/api/" +
            this.route.params.workspace +
            "/bank_payments/" +
            this.route.params.bank_payment,
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
      let val = (value / 1).toFixed(2).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },
  },
};
</script>
