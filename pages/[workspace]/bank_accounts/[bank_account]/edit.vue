<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <div class="flex items-center justify-between">
          <h4
            class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight dark:text-white"
          >
            Nastavení účtu
          </h4>
        </div>
      </template>
    </page-content-header>

    <form v-on:submit.prevent="updateBankAccount">
      <div class="mb-4">
        <div
          class="mb-7 rounded-md border border-gray-200 bg-white px-5 py-5 text-gray-700 md:w-1/2 dark:border-zinc-800 dark:bg-zinc-900"
        >
          <div class="mb-4">
            <label
              for="email"
              class="block text-base font-medium leading-6 text-gray-900 dark:text-zinc-300"
              >Typ účtu</label
            >
            <div class="mt-2 text-gray-500 dark:text-zinc-400">
              <p v-if="bank_account.bank === 'fio'">
                Fio Banka (automatická synchronizace)
              </p>

              <p v-if="bank_account.bank === 'moneta'">
                Moneta (automatická synchronizace)
              </p>

              <p v-if="bank_account.bank === 'komercni_banka_csv'">
                Komerční banka (import ze souboru)
              </p>
            </div>
          </div>

          <div class="mb-4">
            <label
              for="email"
              class="block text-base font-medium leading-6 text-gray-900 dark:text-zinc-200"
              >Název účtu</label
            >
            <div class="mt-2">
              <input
                name="name"
                required
                v-model="name"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
                placeholder="Firemní účet (hlavní)"
              />
            </div>
          </div>

          <div class="mb-4">
            <label
              for="email"
              class="block text-base font-medium leading-6 text-gray-900 dark:text-zinc-200"
              >Číslo účtu</label
            >
            <div class="mt-2">
              <input
                v-model="account_number"
                required
                type="text"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
                placeholder="1234567890"
              />
            </div>
          </div>

          <div>
            <label
              for="email"
              class="block text-base font-medium leading-6 text-gray-900 dark:text-zinc-200"
              >Kód banky</label
            >
            <div class="mt-2">
              <input
                v-model="bank_number"
                required
                name="number"
                id="email"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
                placeholder="1234"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="mb-4" v-if="bank_account.bank === 'fio'">
        <div
          class="mb-7 rounded-md border border-gray-200 bg-white px-5 py-5 text-gray-700 md:w-1/2 dark:border-zinc-800 dark:bg-zinc-900"
        >
          <div class="">
            <label
              for="email"
              class="block text-base font-medium leading-6 text-gray-900"
              >Token pro synchronizaci s Fio Bankou</label
            >

            <div class="mb-5 mt-2">
              <input
                required
                type="password"
                v-model="api_token"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
                placeholder=""
              />
            </div>

            <p
              class="text-gay-400 me-2 inline-block"
              v-if="bank_account.synced_at"
            >
              Platby staženy {{ formatDate(bank_account.synced_at) }}.
            </p>

            <a @click="syncFioNow()" class="me-2 cursor-pointer"
              >Synchronizovat nyní</a
            >
          </div>
        </div>
      </div>

      <div class="mb-4" v-if="bank_account.bank === 'moneta'">
        <div
          class="mb-7 rounded-md border border-gray-200 bg-white px-5 py-5 text-gray-700 md:w-1/2 dark:border-zinc-800 dark:bg-zinc-900"
        >
          <div class="">
            <label
              for="email"
              class="block text-base font-medium leading-6  dark:text-zinc-300 text-gray-900"
              >Token pro synchronizaci s Moneta bankou</label
            >

            <div class="mb-5 mt-2">
              <input
                required
                type="password"
                v-model="api_token"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
                placeholder=""
              />
            </div>

            <p
              class="text-gay-400 me-2 inline-block"
              v-if="bank_account.synced_at"
            >
              Platby staženy {{ formatDate(bank_account.synced_at) }}.
            </p>

            <a @click="syncMonetaNow()" class="me-2 cursor-pointer dark:text-zinc-300"
              >Synchronizovat nyní</a
            >
          </div>
        </div>
      </div>

      <button
        class="mb-10 cursor-pointer rounded-md bg-black px-4 py-2 font-medium text-white duration-200 hover:bg-gray-700 dark:bg-zinc-900 dark:hover:bg-zinc-800"
        type="submit"
      >
        Uložit změny
      </button>

      <p @click="deleteAccount" class="cursor-pointer text-red-700">
        Smazat bankovní účet
      </p>
    </form>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

useHead({
  title: "Nastavení bankovního účtu",
});
</script>

<script>
import bank_payment from "~/pages/[workspace]/bank_payments/[bank_payment]/index.vue";
import { DateTime } from "luxon";

export default {
  data() {
    return {
      route: null,
      loaded: false,

      bank_account: null,
      name: "",
      account_number: "",
      bank_number: "",
      api_token: "",
    };
  },

  async mounted() {
    this.route = useRoute();

    await this.fetchData();
    this.loaded = true;
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_account", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/bank_accounts/" +
            route.params.bank_account,
          {
            method: "GET",
          },
        ),
      );

      this.bank_account = data.value;

      this.name = data.value.name;
      this.account_number = data.value.account_number;
      this.bank_number = data.value.bank_number;
      this.api_token = data.value.api_token;
    },

    async updateBankAccount() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_account", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/bank_accounts/" +
            route.params.bank_account,
          {
            method: "PATCH",
            body: {
              name: this.name,
              account_number: this.account_number,
              bank_number: this.bank_number,
              api_token: this.api_token,
            },
          },
        ),
      );

      await navigateTo(
        "/" +
          route.params.workspace +
          "/bank_accounts/" +
          this.bank_account.uuid,
      );
    },

    formatDate(date) {
      let formatted = DateTime.fromISO(date);

      return formatted.toFormat("d.M.yyyy v H:mm");
    },

    async syncFioNow() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_account", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/bank_accounts/" +
            route.params.bank_account +
            "/sync-fio",
          {
            method: "POST",
            body: {
              name: this.name,
              account_number: this.account_number,
              bank_number: this.bank_number,
              api_token: this.api_token,
            },
          },
        ),
      );

      await this.fetchData();
    },

    async syncMonetaNow() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_account", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/bank_accounts/" +
            route.params.bank_account +
            "/sync-moneta",
          {
            method: "POST",
            body: {
              name: this.name,
              account_number: this.account_number,
              bank_number: this.bank_number,
              api_token: this.api_token,
            },
          },
        ),
      );

      await this.fetchData();
    },

    async deleteAccount() {
      const confirm = window.confirm(
        "Opravdu chcete odstranit tento bankovní účet a všechny související bankovní platby? Bude zrušeno párování se všemi příjmy a výdaji.",
      );

      if (!confirm) {
        return;
      }

      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("bank_account", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/bank_accounts/" +
            route.params.bank_account,
          {
            method: "DELETE",
          },
        ),
      );

      await navigateTo("/" + route.params.workspace + "/bank_accounts");
    },
  },
};
</script>
