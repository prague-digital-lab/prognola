<template>
  <div>
    <div class="mb-4 md:flex md:items-center md:justify-between">
      <div class="min-w-0 flex-1">
        <h4
          class="mb-4 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          Účty
        </h4>
      </div>
    </div>

    <div
      class="mb-4 divide-y divide-gray-200 rounded border border-gray-200"
      v-if="bank_accounts.length > 0"
    >
      <bank-account-row
        v-for="bank_account in bank_accounts"
        :bank_account="bank_account"
      ></bank-account-row>
    </div>

    <div
      v-if="bank_accounts.length === 0 && loaded"
      class="flex h-[400px] w-full items-center rounded-2xl border border-gray-200 bg-white px-10 text-gray-700"
    >
      <div class="md:w-1/2">
        <p class="mb-4 font-medium">
          Mějte všechny platby na jednom místě
        </p>

        <p class="mb-7 font-light text-sm">
          Přidejte bankovní účet nebo hotovostní pokladnu, abyste měli vždy
          přehled o&nbsp;uskutečněných platbách. Teď už vám žádná transakce
          neunikne.
        </p>

        <nuxt-link
          :href="'/' + route.params.workspace + '/bank_accounts/new'"
          class="rounded-xl bg-black px-4 py-2 font-medium text-white"
          >Přidat účet</nuxt-link
        >
      </div>
    </div>
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
