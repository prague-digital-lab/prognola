<template>
  <div class="relative mb-3 rounded text-base text-gray-700 hover:bg-gray-100 dark:text-zinc-400 dark:hover:bg-zinc-900">
    <Transition>
      <div
        v-if="select_expanded"
        class="absolute left-[-150px] top-[-5px] w-[147px] rounded border border-slate-200 bg-white px-1 py-1 shadow"
      >
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('draft')"
        >
          <expense-status-icon payment_status="draft" class="me-2" />

          Ke zpracování
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('plan')"
        >
          <expense-status-icon payment_status="plan" class="me-2" />

          Odhad
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('pending')"
        >
          <expense-status-icon payment_status="pending" class="me-2" />

          K úhradě
        </div>
        <div
          class="rounded px-1 py-1 hover:bg-gray-100"
          @click="setStatus('paid')"
        >
          <expense-status-icon payment_status="paid" class="me-2" />

          Uhrazeno
        </div>
      </div>
    </Transition>

    <div @click="expandSelect" class="px-1 py-1">
      <div v-if="status === 'draft'">
        <expense-status-icon :expense="expense" class="me-2" />

        Ke zpracování
      </div>
      <div v-if="status === 'plan'">
        <expense-status-icon :expense="expense" class="me-2" />
        Odhad
      </div>
      <div v-if="status === 'pending'">
        <expense-status-icon :expense="expense" class="me-2" />

        <span v-if="!isDue">k úhradě</span>
        <span v-else>k úhradě (po splatnosti)</span>
      </div>
      <div v-if="status === 'paid'">
        <expense-status-icon :expense="expense" class="me-2" />

        Uhrazeno
      </div>
    </div>
  </div>
</template>

<script>
import { DateTime } from "luxon";

export default {
  data: () => {
    return {
      select_expanded: false,
    };
  },

  props: ["expense"],

  mounted() {
    // this.status = this.expense.payment_status;
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
      this.select_expanded = false;

      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("expense", () =>
        client(
          "/api/" + route.params.workspace + "/expenses/" + this.expense.uuid,
          {
            method: "PATCH",
            body: {
              payment_status: status,
            },
          },
        ),
      );

      this.$emit("expense-updated");
    },
  },

  computed: {
    status() {
      return this.expense.payment_status;
    },

    isDue() {
      const today = DateTime.now().endOf("day");

      const paid_at = DateTime.fromISO(this.expense.paid_at);

      return paid_at < today;
    },
  },
};
</script>

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
