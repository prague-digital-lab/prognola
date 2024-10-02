<template>
  <div
    class="flex cursor-pointer items-center justify-between px-5 py-4"
    @click="navigateToBankAccount"
  >
    <div class="flex text-sm">
      <!--      <p class="w-[60px] font-light text-gray-500">{{ bank_account.id }}</p>-->
      <p class="w-[400px]">{{ bank_account.name }}</p>
      <p class="w-[400px]">{{ bank_account.bank }}</p>
    </div>

    <div class="flex items-center text-sm font-light text-slate-600">
      <p class="font-bold">{{ formatPrice(bank_account.current_amount) }} Kč</p>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { DateTime } from "luxon";
import bank_accounts from "../../pages/[workspace]/bank_accounts/index.vue";

export default defineComponent({
  name: "BankAccountRow",
  computed: {
    bank_accounts() {
      return bank_accounts;
    },
  },
  props: ["bank_account"],
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
          "/bank_accounts/" +
          this.bank_account.uuid,
      );
    },
  },
});
</script>

<style scoped></style>
