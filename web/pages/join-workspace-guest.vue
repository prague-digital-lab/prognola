<template>
  <div class="flex flex-col items-center text-center">
    <h1 class="mb-3 text-2xl text-gray-200">Vítejte v Prognole</h1>

    <p class="mb-5 text-gray-400">Připojte se do organizace v Prognole</p>

    <div
      class="flex w-1/2 select-none flex-col space-y-4 rounded-2xl border border-gray-800 bg-gray-700/20 px-5 py-5 text-center text-gray-200"
    >
      <p v-if="email" class="text-gray-300">
        Pro připojení k organizaci se musíte přihlásit jako
        <span class="text-white">{{ email }}</span
        >.
      </p>
      <p v-else>Pro připojení k organizaci se musíte přihlásit.</p>
      <div class="grid grid-cols-2 gap-2">
        <div
          @click="login"
          class="cursor-pointer rounded-md border border-gray-800 bg-gray-700 px-3 py-1 text-sm"
        >
          Přihlásit se
        </div>

        <div
          @click="register"
          class="cursor-pointer rounded-md border border-gray-800 bg-gray-700 px-3 py-1 text-sm"
        >
          Registrovat účet
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "guest",
});

useHead({
  title: "Pozvánka do Prognola",
});

const email = ref("");

onMounted(async () => {
  const route = useRoute();
  email.value = route.query.email;

  const { isAuthenticated } = useSanctumAuth();
  const user = useSanctumUser();

  if (isAuthenticated.value === true && email.value === user.value.email) {
    await navigateTo("/join-workspace");
  }
});

async function login() {
  const { isAuthenticated } = useSanctumAuth();
  if (isAuthenticated.value === true) {
    const { logout } = useSanctumAuth();

    await logout();
  }

  await navigateTo("/login");
}

async function register() {
  const { isAuthenticated } = useSanctumAuth();
  if (isAuthenticated.value === true) {
    const { logout } = useSanctumAuth();

    await logout();
  }

  await navigateTo("/register");
}
</script>
