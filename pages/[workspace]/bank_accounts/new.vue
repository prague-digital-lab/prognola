<template>
  <div>
    <div class="mb-4 md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Vytvořte nový účet
        </h4>
      </div>
    </div>

    <div>
      <div class="mb-4">
        <p class="text-gray-600">1. Zvolte typ účtu</p>
      </div>

      <div class="mb-10 grid w-full grid-flow-col gap-7 duration-500">
        <choose-account-type
          :selected="account_type === 'bank_account'"
          name="Bankovní účet"
          account_type="bank_account"
          @click="account_type = 'bank_account'"
        ></choose-account-type>

        <choose-account-type
          :selected="account_type === 'cash_register'"
          name="Hotovostní pokladna"
          account_type="cash_register"
          @click="account_type = 'cash_register'"
        ></choose-account-type>
      </div>
    </div>

    <div v-if="account_type === 'bank_account'">
      <div class="mb-4">
        <p class="text-gray-600">2. Vyberte banku</p>
      </div>

      <div>
        <div class="mb-10 grid w-full grid-cols-6 gap-5 duration-500">
          <choose-bank-button
            :selected="selected_bank === 'fio'"
            name="Fio Banka"
            @click="selected_bank = 'fio'"
          ></choose-bank-button>

          <choose-bank-button
            :selected="selected_bank === 'kb'"
            name="Komerční banka"
            @click="selected_bank = 'kb'"
          ></choose-bank-button>

          <choose-bank-button
            :selected="selected_bank === 'other'"
            name="Jiná banka"
            @click="selected_bank = 'other'"
          ></choose-bank-button>
        </div>
      </div>
    </div>

    <div v-if="selected_bank !== ''">
      <div class="mb-4">
        <p class="text-gray-600 mb-4">3. Podrobnosti o účtu</p>

        <div
          class="mb-7 rounded-xl border border-gray-200 bg-white px-5 py-7 text-gray-700 md:w-1/2"
        >
          <div class="mb-4">
            <label
              for="email"
              class="block text-sm font-medium leading-6 text-gray-900"
              >Název účtu</label
            >
            <div class="mt-2">
              <input
                name="name"
                v-model="name"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                placeholder="Firemní účet (hlavní)"
              />
            </div>
          </div>

          <div class="mb-4">
            <label
              for="email"
              class="block text-sm font-medium leading-6 text-gray-900"
              >Číslo účtu</label
            >
            <div class="mt-2">
              <input
                v-model="bank_number"
                type="number"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                placeholder="1234567890"
              />
            </div>
          </div>

          <div>
            <label
              for="email"
              class="block text-sm font-medium leading-6 text-gray-900"
              >Kód banky</label
            >
            <div class="mt-2">
              <input
                type="email"
                name="email"
                id="email"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                placeholder="1234"
              />
            </div>
          </div>
        </div>

        <nuxt-link
          class="rounded-xl bg-black px-4 py-2 font-medium text-white"
          v-on:click="createBankAccount"
          >Vytvořit účet
        </nuxt-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import ChooseBankButton from "~/components/bank_account/new/ChooseBankButton.vue";
import ChooseAccountType from "~/components/bank_account/new/ChooseAccountType.vue";

useHead({
  title: "Účty - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const route = useRoute();
const client = useSanctumClient();

const account_type = ref("");
const selected_bank = ref("");
const name = ref("");
const bank_number = ref("");

async function createBankAccount() {
  const { data } = await useAsyncData("bank_account", () =>
    client("/api/" + route.params.workspace + "/bank_accounts", {
      method: "POST",
      body: {
        name: name.value,
        bank: selected_bank.value,
        bank_number: bank_number.value,
      },
    }),
  );

  await navigateTo("/" + route.params.workspace + "/bank_accounts");
}
</script>

<script>
export default {
  data() {
    return {
      // Page UI data
      loaded: false,
      bank_accounts: [],
    };
  },

  mounted() {
    this.fetchData();
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client("/api/" + route.params.workspace + "/bank_accounts", {
          method: "GET",
        }),
      );

      this.bank_accounts = data.value;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },
  },
};
</script>

<style lang="scss">
.bg-grad-select {
  background: rgb(0, 0, 0);
  background: linear-gradient(
    180deg,
    rgba(234, 234, 234, 0.24) 0%,
    rgba(255, 255, 255, 0.61) 65%,
    rgba(243, 243, 243, 0.29) 100%
  );
}
</style>
