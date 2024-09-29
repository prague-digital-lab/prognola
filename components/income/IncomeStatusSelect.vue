<script>
export default {
  data: () => {
    return {
      status: null,
      select_expanded: false,
    };
  },

  props: ["income"],

  mounted() {
    this.status = this.income.payment_status;
  },

  methods: {
    expandSelect() {
      this.select_expanded = !this.select_expanded;
    },

    async setStatus(status) {
      this.status = status;
      this.select_expanded = false;

      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("income", () =>
        client(
          "/api/" + route.params.workspace + "/incomes/" + this.income.uuid,
          {
            method: "PATCH",
            body: {
              payment_status: status,
            },
          },
        ),
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
          <income-status-icon payment_status="draft" />

          Ke zpracování
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('plan')"
        >
          <income-status-icon payment_status="plan" />

          Odhad
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('pending')"
        >
          <income-status-icon payment_status="pending" />

          K úhradě
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('paid')"
        >
          <income-status-icon payment_status="paid" />

          Uhrazeno
        </div>
      </div>
    </Transition>

    <div @click="expandSelect" class="px-1 py-1">
      <div v-if="status === 'draft'">
        <income-status-icon payment_status="draft" />

        Ke zpracování
      </div>
      <div v-if="status === 'plan'">
        <income-status-icon payment_status="plan" />
        Odhad
      </div>
      <div v-if="status === 'pending'">
        <income-status-icon payment_status="pending" />

        K úhradě
      </div>
      <div v-if="status === 'paid'">
        <income-status-icon payment_status="paid" />

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
