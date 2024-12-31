<template>
  <div>
    <page-content-header>
      <template v-slot:title>
        <heading>Účty</heading>
      </template>
    </page-content-header>

    <div
      class="mb-4 divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white dark:divide-zinc-800 dark:border-zinc-800"
      v-if="bank_accounts.length > 0"
    >
      <bank-account-row
        v-for="bank_account in bank_accounts"
        :bank_account="bank_account"
      ></bank-account-row>
    </div>

    <div class="text-end text-base" v-if="bank_accounts.length > 0">
      <nuxt-link
        :href="'/' + route.params.workspace + '/bank_accounts/new'"
        class="rounded bg-indigo-700 px-3 py-1 text-gray-100 transition hover:bg-indigo-900"
        >Přidat účet
      </nuxt-link>
    </div>

    <div
      v-if="bank_accounts.length === 0 && loaded"
      class="flex h-[400px] w-full items-center rounded-2xl border border-gray-200 bg-white px-10 text-gray-700 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-200"
    >
      <div class="md:w-1/2">
        <p class="mb-4 text-xl font-medium">
          Mějte všechny platby na jednom místě
        </p>

        <p class="mb-7 text-base font-light dark:text-zinc-400">
          Přidejte bankovní účet nebo hotovostní pokladnu, abyste měli vždy
          přehled o&nbsp;uskutečněných platbách. Teď už vám žádná transakce
          neunikne.
        </p>

        <nuxt-link
          :href="'/' + route.params.workspace + '/bank_accounts/new'"
          class="rounded-md bg-black px-4 py-2 text-base font-medium text-white duration-150 hover:bg-gray-700 dark:border dark:border-zinc-800"
          >Přidat účet
        </nuxt-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import Heading from "~/components/ui/Heading.vue";

useHead({
  title: "Účty",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const route = useRoute();
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

      this.bank_accounts = await client(
        "/api/" + route.params.workspace + "/bank_accounts",
        {
          method: "GET",
        },
      );

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },
  },
};
</script>
