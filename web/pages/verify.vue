<template>
  <div>
    <div
      class="rounded-2xl border border-gray-800 bg-gray-700/20 px-5 py-5 text-center text-gray-200"
    >
      <h1 class="mb-5 text-3xl">Ověření emailu</h1>

      <p>
        Právě jsme vám poslali odkaz na email <b>{{ user ? user.email : "" }}</b
        >.
      </p>

      <p class="text-gray-300">
        Nedošel vám email?
        <a @click="resend" class="cursor-pointer text-indigo-400"
          >Zkusit poslat znovu.</a
        >
      </p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "guest",
  middleware: ["sanctum:auth", "sanctum:not-verified"],
});

useHead({
  title: "Ověření účtu",
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
    const { user } = useSanctumAuth();
    this.user = user;
  },

  methods: {
    async resend() {
      const client = useSanctumClient();

      const { data } = await useAsyncData("income", () =>
        client("/api/email/verify/resend", {
          method: "POST",
        }),
      );
    },
  },
};
</script>
