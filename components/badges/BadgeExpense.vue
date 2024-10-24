<template>
  <nuxt-link
    @click="navigateToExpense"
    class="inline-flex cursor-pointer select-none items-center rounded-md border border-gray-200 bg-white py-[2px] pe-2 ps-2 text-gray-700 duration-100 hover:bg-gray-hover active:bg-gray-100 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:bg-zinc-900"
  >
    <expense-status-icon
      class="me-1"
      :payment_status="expense.payment_status"
    />

    {{ expense.description }}
  </nuxt-link>
</template>

<script setup lang="ts">
const props = defineProps(["expense"]);
const route = useRoute();

async function navigateToExpense() {
  await navigateTo(
    "/" + route.params.workspace + "/expenses/" + props.expense.uuid,
  );
}
</script>

<style scoped></style>
