<template>
  <div
    class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900"
  >
    <nuxt-link href="bank_accounts">
      <div
        class="mb-3 flex items-center justify-between border-b border-gray-200 px-4 pb-2 pt-3 dark:border-zinc-800"
      >
        <div class="truncate text-gray-600 dark:text-zinc-300">
          Dostupné peníze
        </div>
        <div class="text-xl font-semibold tracking-tight dark:text-zinc-200">
          {{ formatPrice(current_balance) }} Kč
        </div>
      </div>
    </nuxt-link>

    <div class="px-4 py-1">
      <p class="mb-2 text-sm text-zinc-500">Bankovní účty</p>

      <div class="divide-y">
        <div
          v-for="bank_account in bank_accounts"
          :key="bank_account.uuid"
          class="flex justify-between py-2"
        >
          <div>{{ bank_account.name }}</div>
          <div class="text-zinc-600 dark:text-zinc-400">{{ formatPrice(bank_account.current_amount) }} Kč</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { formatPrice } from "~/lib/dexie/data_helpers";
import { getBankAccounts } from "~/lib/dexie/repository/bank_account_repository";
import { getCurrentBalance } from "~/lib/dexie/repository/bank_account_repository.js";

const current_balance = ref(0);
const bank_accounts = ref([]);

onMounted(async () => {
  current_balance.value = await getCurrentBalance();
  bank_accounts.value = await getBankAccounts();
});
</script>

<style scoped></style>
