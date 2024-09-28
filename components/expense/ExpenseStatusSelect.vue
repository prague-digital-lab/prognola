<script>
export default {
  data: () => {
    return {
      status: null,
      select_expanded: false,
    };
  },

  props: ["expense"],

  mounted() {
    this.status = this.expense.payment_status;
  },

  methods: {
    expandSelect() {
      if (this.select_expanded === true) {
        this.select_expanded = false;
      } else {
        this.select_expanded = true;
      }
    },

    async setStatus(status) {
      this.status = status;
      this.select_expanded = false;

      const client = useSanctumClient();

      const { data } = await useAsyncData("expense", () =>
        client("/api/expenses/" + this.expense.id, {
          method: "PATCH",
          body: {
            payment_status: status,
          },
        }),
      );
    },
  },
};
</script>

<template>
  <div class="relative mb-3 rounded text-sm text-gray-700 hover:bg-gray-100">
    <Transition>
      <div
        v-if="select_expanded"
        class="absolute left-[-150px] top-[-5px] w-[147px] rounded border border-slate-200 bg-white px-1 py-1 shadow"
      >
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('draft')"
        >
          <expense-status-icon payment_status="draft" />

          Ke zpracování
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('plan')"
        >
          <expense-status-icon payment_status="plan" />

          Odhad
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('pending')"
        >
          <expense-status-icon payment_status="pending" />

          K úhradě
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('paid')"
        >
          <expense-status-icon payment_status="paid" />

          Uhrazeno
        </div>
      </div>
    </Transition>

    <div @click="expandSelect" class="px-1 py-1">
      <div v-if="status === 'draft'">
        <expense-status-icon payment_status="draft" />

        Ke zpracování
      </div>
      <div v-if="status === 'plan'">
        <expense-status-icon payment_status="plan" />
        Odhad
      </div>
      <div v-if="status === 'pending'">
        <expense-status-icon payment_status="pending" />

        K úhradě
      </div>
      <div v-if="status === 'paid'">
        <expense-status-icon payment_status="paid" />

        Uhrazeno
      </div>
    </div>
  </div>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.1s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
