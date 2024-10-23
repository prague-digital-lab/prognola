<template>
  <div
    class="flex items-center justify-between bg-white px-3 py-2 duration-100 hover:bg-gray-hover"
  >
    <!--    <div class="text-base flex">-->
    <!--      <p class="text-gray-500 w-[60px] font-light">{{ bank_payment.id }}</p>-->
    <!--      <p class="w-[400px]"></p>-->
    <!--    </div>-->

    <!--    <div class="text-base text-slate-600 font-light flex items-center">-->
    <!--      <p class="font-bold">{{ formatPrice(bank_payment.amount) }} Kč</p>-->
    <!--    </div>-->

    <table class="w-full text-base">
      <tbody>
        <tr>
          <td class="w-[7%]">{{ formatDate(bank_payment.issued_at) }}</td>
          <td class="w-[30%] pe-5">
            <a class="text-dark text-decoration-none text-sm">{{
              bank_payment.description
            }}</a>

            <div class="text-decoration-none text-sm text-gray-500">
              {{ bank_payment.sender_comment }}
            </div>

            <!--        @if($payment->received_invoices->count() > 0)-->
            <!--        <br>-->

            <!--        @foreach($payment->received_invoices as $received_invoice)-->
            <!--        @include('admin.received_invoice.components.badge', ['received_invoice' => $received_invoice])-->
            <!--        @endforeach-->
            <!--        @endif-->
          </td>
          <td class="w-[10%]">
            <span v-if="bank_payment.type === 'income'"
              >{{ formatPrice(bank_payment.amount) }} Kč</span
            >
            <span class="text-red-700" v-else
              >{{ formatPrice(bank_payment.amount) }} Kč</span
            >
          </td>
          <td class="w-[30%] space-x-1 space-y-2">
            <div
              v-for="income in bank_payment.incomes"
              class="inline-block text-sm text-gray-600"
            >
              <badge-income :income="income" class="" />
            </div>

            <div
              v-for="expense in bank_payment.expenses"
              class="inline-block text-sm text-gray-600"
            >
              <badge-expense :expense="expense" class="" />
            </div>

            <nuxt-link
              :href="
                '/' +
                route.params.workspace +
                '/bank_payments/' +
                bank_payment.uuid +
                '/pair'
              "
              class="inline-block rounded-md border border-gray-200 px-2 py-[2px] text-gray-500 hover:bg-gray-100 active:bg-gray-200"
              >+
            </nuxt-link>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
const route = useRoute();
</script>

<script>
import { defineComponent } from "vue";
import { DateTime } from "luxon";
import BadgeIncome from "~/components/badges/BadgeIncome.vue";
import BadgeExpense from "~/components/badges/BadgeExpense.vue";

export default defineComponent({
  components: { BadgeExpense, BadgeIncome },
  props: ["bank_payment"],
  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    formatDate(date) {
      let formatted = DateTime.fromISO(date);

      return formatted.toFormat("d.M.yyyy");
    },
  },
});
</script>

<style scoped></style>
