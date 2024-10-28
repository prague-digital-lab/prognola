<template>
  <div
    class="flex items-center justify-between bg-white px-3 py-2 hover:bg-gray-hover dark:bg-zinc-900 dark:hover:bg-zinc-800"
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
          <td class="w-[7%] dark:text-zinc-400">
            {{ formatDate(bank_payment.issued_at) }}
          </td>
          <td class="w-[30%] pe-5">
            <a
              class="text-dark text-decoration-none text-sm dark:text-zinc-200"
              >{{ bank_payment.description }}</a
            >

            <div
              class="text-decoration-none text-sm font-light text-gray-500 dark:text-zinc-400"
            >
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
            <bank-payment-amount
              :bank_payment="bank_payment"
            ></bank-payment-amount>
          </td>
          <td class="w-[30%] space-y-2">
            <div
              v-for="income in bank_payment.incomes"
              class="me-1 inline-block text-sm text-gray-600"
            >
              <badge-income :income="income" class="" />
            </div>

            <div
              v-for="expense in bank_payment.expenses"
              class="me-1 inline-block text-sm text-gray-600"
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
            >
              <button-secondary>+</button-secondary>
            </nuxt-link>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";

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
