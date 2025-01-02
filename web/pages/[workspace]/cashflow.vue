<template>
  <div>
    <div>
      <div class="mb-7">
        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
          <income-widget></income-widget>

          <expense-widget></expense-widget>

          <bank-account-widget></bank-account-widget>
        </dl>
      </div>

      <cashflow-widget></cashflow-widget>
    </div>
  </div>
</template>

<script setup>
import IncomeWidget from "~/components/dashboard/IncomeWidget.vue";
import ExpenseWidget from "~/components/dashboard/ExpenseWidget.vue";
import BankAccountWidget from "~/components/dashboard/BankAccountWidget.vue";
import CashflowWidget from "~/components/dashboard/CashflowWidget.vue";

useHead({
  title: "Cashflow",
});

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});
</script>
