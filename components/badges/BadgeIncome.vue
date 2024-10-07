<template>
  <nuxt-link
    @click="navigateToIncome"
    class="cursor-pointer select-none rounded-md border border-gray-200 bg-white px-2 py-1 text-gray-700 duration-100 hover:bg-gray-hover active:bg-gray-100"
  >
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
