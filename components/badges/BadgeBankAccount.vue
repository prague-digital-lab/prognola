<template>
  <nuxt-link
    @click="navigateToBankAccount"
    class="cursor-pointer select-none rounded-md border border-gray-200 bg-white px-2 py-1 text-gray-700 duration-100 hover:bg-gray-hover active:bg-gray-100 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-300"
  >
    {{ bank_account.name }}
  </nuxt-link>
</template>

<script setup lang="ts">
const props = defineProps(["bank_account"]);
const route = useRoute();

async function navigateToBankAccount() {
  await navigateTo(
    "/" + route.params.workspace + "/bank_accounts/" + props.bank_account.uuid,
  );
}
</script>

<style scoped></style>
