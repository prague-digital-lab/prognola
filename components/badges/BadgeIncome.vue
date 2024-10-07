<template>
  <nuxt-link
    @click="navigateToIncome"
    class="inline-flex items-center cursor-pointer select-none rounded-md border border-gray-200 bg-white ps-2 pe-2 py-[2px] text-gray-700 duration-100 hover:bg-gray-hover active:bg-gray-100"
  >
    <income-status-icon  class="me-1" :payment_status="income.payment_status" />

    {{ income.name }}
  </nuxt-link>
</template>

<script setup lang="ts">
const props = defineProps(["income"]);
const route = useRoute();

async function navigateToIncome() {
  await navigateTo(
    "/" + route.params.workspace + "/income/" + props.income.uuid,
  );
}
</script>

<style scoped></style>
