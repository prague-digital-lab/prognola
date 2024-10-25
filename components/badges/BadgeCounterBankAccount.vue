<template>
  <nuxt-link
    @click="navigateToBankAccount"
    class="cursor-pointer select-none rounded-md border border-gray-200 bg-white px-2 py-1 text-gray-700 duration-100 hover:bg-gray-hover active:bg-gray-100 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800"
  >
    {{ counter_bank_account.account_number }}/{{ counter_bank_account.bank_number }}
  </nuxt-link>
</template>

<script setup>
const props = defineProps(["counter_bank_account"]);
const route = useRoute();

async function navigateToBankAccount() {
  await navigateTo(
    "/" +
      route.params.workspace +
      "/counter_bank_accounts/" +
      props.counter_bank_account.uuid,
  );
}
</script>

<style scoped></style>
