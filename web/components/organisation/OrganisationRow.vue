<template>
  <div
    class="flex items-center justify-between px-3 py-2 hover:bg-gray-hover dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
    @click="navigateToOrganisation"
  >
    <div class="flex text-base">
      <p class="w-[400px]">
        {{ organisation.name ? organisation.name : "bez názvu" }}
      </p>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { DateTime } from "luxon";

export default defineComponent({
  props: ["organisation"],
  methods: {
    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    formatDate(date) {
      let formatted = DateTime.fromISO(date);

      return formatted.toFormat("d.M.yyyy");
    },

    async navigateToOrganisation() {
      const route = useRoute();

      await navigateTo(
        "/" +
          route.params.workspace +
          "/organisations/" +
          this.organisation.uuid,
      );
    },
  },
});
</script>

<style scoped></style>
