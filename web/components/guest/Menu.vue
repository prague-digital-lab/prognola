<template>
  <div
    class="sticky mb-[50px] mt-5 w-full rounded-2xl border border-gray-800/80 bg-gray-900/50 px-2 py-2 text-white backdrop-blur-3xl"
  >
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <div
          class="rounded-md px-2 py-1 duration-100 hover:bg-zinc-700/20 hover:ring-1 hover:ring-zinc-800"
        >
          <nuxt-link href="/" class="flex items-center text-[15px]">
            <img
              class="me-2 inline-block h-6 w-auto"
              src="/img/logo_prazska_laborator.png"
              alt="Finance"
            />

            Prognola
          </nuxt-link>
        </div>

        <nuxt-link
          href="/cs/plan"
          class="rounded-md px-2 py-1 text-sm text-zinc-200 hover:bg-zinc-700/30"
        >
          Plánování
        </nuxt-link>

        <nuxt-link
          href="/cs/plan"
          class="rounded-md px-2 py-1 text-sm text-zinc-200 hover:bg-zinc-700/30"
        >
          Příjmy a výdaje
        </nuxt-link>
      </div>

      <div v-if="!authenticated">
        <nuxt-link
          href="/login"
          class="me-2 rounded bg-gray-800 px-2.5 py-1.5 text-base font-medium text-gray-200"
        >
          Přihlásit se
        </nuxt-link>
        <nuxt-link
          href="/register"
          class="rounded bg-gray-200 px-2.5 py-1.5 text-base font-medium text-gray-800"
        >
          Vyzkoušet
        </nuxt-link>
      </div>

      <div v-if="authenticated && !verified">
        <a
          @click="logout"
          class="me-2 rounded bg-gray-800 px-2.5 py-1.5 text-base font-medium text-gray-200"
        >
          Odhlásit se</a
        >

        <nuxt-link
          href="/entry"
          class="rounded bg-gray-200 px-2.5 py-1.5 text-base font-medium text-gray-800"
          >Ověřit email
        </nuxt-link>
      </div>

      <div v-if="authenticated && verified">
        <a
          @click="logout"
          class="me-2 rounded bg-gray-800 px-2.5 py-1.5 text-base font-medium text-gray-200"
        >
          Odhlásit se</a
        >

        <nuxt-link
          href="/entry"
          class="rounded bg-gray-200 px-2.5 py-1.5 text-base font-medium text-gray-800"
        >
          Přejít do aplikace
        </nuxt-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

const { user } = useSanctumAuth();

const authenticated = computed(() => {
  return user.value !== null;
});

const verified = computed(() => {
  return authenticated && user.value.email_verified_at;
});

async function logout() {
  const { logout } = useSanctumAuth();

  await logout();
}

const sidebarOpen = ref(false);
</script>

<style scoped></style>
