<template>
  <div class="p-3 dark:bg-zinc-900">
    <div class="mb-2 flex items-center justify-between">
      <p class="md:w-[400px] dark:text-white">
        {{ expense.description ? expense.description : "nový výdaj" }}
      </p>

      <div class="flex items-center">
        <div class="w-[120px]">
          <expense-status-badge :expense="expense"></expense-status-badge>
        </div>

        <div class="ms-2">
          <expense-status-icon :expense="expense" :key="expense.uuid" />
        </div>
      </div>
    </div>

    <div class="flex justify-between">
      <div
          v-if="organisation"
          class="me-2 flex cursor-pointer items-center rounded-md border border-gray-200 px-2 py-[1px] text-[12px] leading-5 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400"
      >
        <building-library-icon
            :key="expense.uuid"
            class="me-1 h-4 w-4"
        ></building-library-icon>

        {{ organisation.name }}
      </div>
      <div v-else></div>

      <div class="flex items-center justify-between">
        <context-menu-paid-at-picker
            :key="expense.uuid"
            @expense-updated="$emit('expense-updated')"
            :expense="expense"
        ></context-menu-paid-at-picker>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">

import ContextMenuPaidAtPicker from "~/components/expense/expense_row/ContextMenuPaidAtPicker.vue";
import {BuildingLibraryIcon} from "@heroicons/vue/24/outline/index";
import ExpenseStatusBadge from "~/components/expense/expense_row/ExpenseStatusBadge.vue";

const props = defineProps(["expense", 'organisation']);
</script>

<style scoped>

</style>