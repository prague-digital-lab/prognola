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
        <p class="font-semibold">1. Zvolte typ účtu</p>
      </div>

      <div class="mb-10 grid w-full grid-flow-col gap-7 duration-500">
        <div
          class="bg-grad-select flex h-[200px] items-center justify-center rounded-xl shadow duration-300"
          :class="
            account_type === 'bank_account'
              ? 'ring-1 ring-gray-700 ring-offset-2'
              : ''
          "
        >
          <div class="text-center">
            <p class="mb-4">Bankovní účet</p>

            <nuxt-link
              class="rounded-xl bg-black px-4 py-2 font-medium text-white"
              v-if="account_type !== 'bank_account'"
              @click="account_type = 'bank_account'"
              >Vybrat
            </nuxt-link>

            <nuxt-link
              class="rounded-xl bg-gray-500 px-4 py-2 font-medium text-white"
              v-else
              >Zvoleno
            </nuxt-link>
          </div>
        </div>
        <div
          class="bg-grad-select flex h-[200px] items-center justify-center rounded-xl shadow duration-300"
          :class="
            account_type === 'cash_register'
              ? 'ring-1 ring-gray-700 ring-offset-2'
              : ''
          "
        >
          <div class="text-center">
            <p class="mb-4">Hotovostní pokladna</p>

            <nuxt-link
              class="rounded-xl bg-black px-4 py-2 font-medium text-white"
              v-if="account_type !== 'cash_register'"
              @click="account_type = 'cash_register'"
              >Vybrat
            </nuxt-link>

            <nuxt-link
              class="rounded-xl bg-gray-500 px-4 py-2 font-medium text-white"
              v-else
              >Zvoleno
            </nuxt-link>
          </div>
        </div>
      </div>
    </div>

    <div v-if="account_type === 'bank_account'">
      <div class="mb-4">
        <p class="font-semibold">2. Vyberte poskytovatele</p>
      </div>

      <div>
        <div class="mb-10 grid w-full grid-cols-6 gap-5 duration-500">
          <div
            class="bg-grad-select flex h-[100px] items-center justify-center rounded-xl shadow duration-300"
            :class="
              selected_bank === 'fio'
                ? 'ring-1 ring-gray-700 ring-offset-2'
                : ''
            "
            @click="selected_bank = 'fio'"
          >
            <div class="text-center">
              <p class="">Fio Banka</p>
            </div>
          </div>

          <div
            class="bg-grad-select flex h-[100px] items-center justify-center rounded-xl shadow duration-300"
            :class="
              selected_bank === 'kb' ? 'ring-1 ring-gray-700 ring-offset-2' : ''
            "
            @click="selected_bank = 'kb'"
          >
            <div class="text-center">
              <p class="">Komerční banka</p>
            </div>
          </div>

          <div
            class="bg-grad-select flex h-[100px] items-center justify-center rounded-xl shadow duration-300"
            :class="
              selected_bank === 'other'
                ? 'ring-1 ring-gray-700 ring-offset-2'
                : ''
            "
            @click="selected_bank = 'other'"
          >
            <div class="text-center">
              <p class="">Jiná banka</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="selected_bank !== ''">
      <div class="mb-4">
        <p class="mb-4 font-semibold">3. Podrobnosti o účtu</p>

        <div class="overflow-hidden rounded-lg bg-white shadow md:w-1/2">
          <div class="px-4 py-5 sm:p-6">
            <div class="mb-4">
              <label
                for="email"
                class="block text-sm font-medium leading-6 text-gray-900"
                >Název účtu</label
              >
              <div class="mt-2">
                <input
                  type="email"
                  name="email"
                  id="email"
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
                  type="email"
                  name="email"
                  id="email"
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
        </div>

        <a>ODeslat</a>
      </div>
    </div>
    <!--    <div class="mb-5">-->
    <!--      <p>Název účtu</p>-->

    <!--      <input  />-->
    <!--    </div>-->

    <!--    <nuxt-link class="rounded-xl bg-black px-4 py-2 font-medium text-white"-->
    <!--      >Přidat první účet-->
    <!--    </nuxt-link>-->
  </div>
</template>

<script setup>
useHead({
  title: "Účty - Prognola",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const route = useRoute();

const account_type = ref("");
const selected_bank = ref("");
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

    async createBankAccount() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("expense", () =>
        client("/api/" + route.params.workspace + "/bank_accounts", {
          method: "POST",
          body: {
            description: this.new_expense_name,
            price: 0,
            paid_at: this.from,
          },
        }),
      );

      let id = data.value.id;

      await navigateTo("/finance/expenses/" + id);
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
