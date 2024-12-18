<template>
  <div>
  <div v-if="expense.payment_status === 'paid'">
    <p
      class="ms-2 text-end font-semibold text-slate-700 dark:text-zinc-300"
    >
      {{ formatPrice(expense.price) }} Kč
    </p>
  </div>

  <div v-else-if="expense.payment_status === 'draft'">
    <p
      class="me-2 text-end font-semibold text-purple-800 dark:text-indigo-500"
    >
      ke zpracování
    </p>
  </div>

  <div v-else-if="expense.payment_status === 'plan'">
    <p class="ms-2 text-end font-semibold text-yellow-500">
      {{ formatPrice(expense.price) }} Kč
    </p>
  </div>

  <div v-else>
    <p class="ms-2 text-end font-bold text-red-700" v-if="isDue">
      {{ formatPrice(expense.price) }} Kč
    </p>

    <p class="ms-2 text-end font-bold text-orange-400" v-else>
      {{ formatPrice(expense.price) }} Kč
    </p>
  </div>
  </div>
</template>

<script setup>
import { DateTime } from "luxon";

const props = defineProps(['expense'])

function formatDate(date) {
  let formatted = DateTime.fromJSDate(date);

  return formatted.toFormat("d.M.yyyy");
}

function formatPrice(value) {
  let val = (value / 1).toFixed(0).replace(".", ",");
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

const isDue = computed(() => {
  const today = DateTime.now().startOf("day");

  const paid_at = DateTime.fromJSDate(props.expense.paid_at);
  return paid_at < today;
});
</script>