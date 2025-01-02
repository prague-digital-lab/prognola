<template>
  <div class="p-3 dark:bg-zinc-900">
    <div class="mb-2 flex items-center justify-between">
      <p class="md:w-[400px] dark:text-white">
        {{ income.name ? income.name : "nový příjem" }}
      </p>

      <div class="flex items-center">
        <div
            v-if="income.payment_status === 'paid'"
            class="w-[120px] dark:text-zinc-300"
        >
          <p class="me-2 text-end">{{ formatPrice(income.amount) }} Kč</p>
        </div>

        <div v-else-if="income.payment_status === 'draft'" class="w-[120px]">
          <p class="me-2 text-end font-semibold text-purple-800">ke zpracování</p>
        </div>

        <div v-else-if="income.payment_status === 'plan'" class="w-[120px]">
          <p class="me-2 text-end font-semibold text-yellow-500">
            ~{{ formatPrice(income.amount) }} Kč
          </p>
        </div>

        <div v-else class="w-[120px]">
          <p class="me-2 text-end font-bold text-orange-400">
            {{ formatPrice(income.amount) }} Kč
          </p>
        </div>
        <div class="">
          <income-status-icon :payment_status="income.payment_status" :key="income.uuid" />
        </div>
      </div>
    </div>

    <div class="flex justify-between">
      <div
          v-if="organisation"
          class="me-2 flex cursor-pointer items-center rounded-md border border-gray-200 px-2 py-[1px] text-[12px] leading-5 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400"
      >
        <building-library-icon
            :key="income.uuid"
            class="me-1 h-4 w-4"
        ></building-library-icon>

        {{ organisation.name }}
      </div>
      <div v-else></div>

      <div class="flex items-center justify-between  text-zinc-500 dark:text-zinc-400">
        {{ formatDate(income.paid_at) }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">

import {BuildingLibraryIcon} from "@heroicons/vue/24/outline/index";
import {formatDate, formatPrice} from "../../../lib/dexie/data_helpers";

const props = defineProps(["income", 'organisation']);
</script>

<style scoped>

</style>