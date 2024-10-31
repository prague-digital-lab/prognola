<template>
  <div
    class="flex items-center justify-between bg-white px-5 py-4 hover:bg-gray-hover dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800"
    @click="navigateToBankAccount"
  >
    <div class="flex text-base">
      <!--      <p class="w-[60px] font-light text-gray-500">{{ counter_bank_account.id }}</p>-->
      <!--      <p class="w-[400px] dark:text-zinc-200">{{ counter_bank_account.name }}</p>-->
      <p class="w-[400px]  dark:text-zinc-400 text-gray-900">
        {{ counter_bank_account.account_number }}/{{
          counter_bank_account.bank_number
        }}
      </p>
    </div>

    <!--    <div class="flex items-center text-base font-light text-slate-600">-->
    <!--      <p class="font-semibold dark:text-zinc-200">-->
    <!--        {{ formatPrice(counter_bank_account.current_amount) }} Kč-->
    <!--      </p>-->
    <!--    </div>-->
  </div>
</template>

<script>
import { DateTime } from "luxon";

export default {
  name: "BankAccountRow",
  props: ["counter_bank_account"],
  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    formatDate(date) {
      let formatted = DateTime.fromISO(date);

      return formatted.toFormat("d.M.yyyy");
    },

    async navigateToBankAccount() {
      const route = useRoute();
      await navigateTo(
        "/" +
          route.params.workspace +
          "/counter_bank_accounts/" +
          this.counter_bank_account.uuid,
      );
    },
  },
};
</script>

<style scoped></style>
