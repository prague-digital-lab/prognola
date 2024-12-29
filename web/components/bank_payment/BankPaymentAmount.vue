<template>
  <div class="flex items-center">
    <minus-circle-icon
      v-if="bank_payment.type === 'expense'"
      class="me-2 inline-block h-4 w-4 text-red-500"
    ></minus-circle-icon>
    <plus-circle-icon
      v-if="bank_payment.type === 'income'"
      class="me-2 inline-block h-4 w-4 text-green-500"
    ></plus-circle-icon>

    <span v-if="bank_payment.amount < 0" class="text-red-500"
      >{{ formatPrice(bank_payment.amount * -1) }} Kč</span
    >
    <span v-else class="text-black dark:text-zinc-200"
      >{{ formatPrice(bank_payment.amount) }} Kč</span
    >
  </div>
</template>

<script setup>
import { MinusCircleIcon, PlusCircleIcon } from "@heroicons/vue/20/solid/index";

const props = defineProps(["bank_payment"]);

function formatPrice(value) {
  let val = (value / 1).toFixed(2).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}
</script>

<style scoped></style>
