<template>
  <div>
    <div
      class="rounded-2xl border border-gray-800 bg-gray-700/20 px-5 py-5 text-center text-gray-200"
    >
      <p>Probíhá ověření účtu.</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "guest",
  middleware: ["sanctum:auth"],
});

useHead({
  title: "Ověření účtu - Prognola",
});
</script>

<script>
export default {
  data: () => {
    return {
      user: null,
    };
  },

  mounted() {
    this.verifyEmail(this.$route.query.url);
  },

  methods: {
    async verifyEmail(url) {
      const client = useSanctumClient();

      const { data } = await useAsyncData("verify-submit", () =>
        client(url, {
          method: "GET",
        }),
      );

      const { refreshIdentity } = useSanctumAuth();
      await refreshIdentity();

      await navigateTo("/join-workspace");
    },
  },
};
</script>
