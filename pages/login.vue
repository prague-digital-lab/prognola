<template>
  <div
    class="flex min-h-full flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8"
  >
    <div class="w-full max-w-sm space-y-10">
      <div>
        <img
          class="mx-auto h-10 w-auto"
          src="/img/logo_prazska_laborator.png"
          alt="Logo"
        />
        <!--        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-200">Vítejte zpět!</h2>-->
      </div>
      <form class="space-y-6" v-on:submit.prevent="submitLogin">
        <div class="">
          <div
            class=""
          />
          <div>
            <input
              v-model="email"
              id="email-address"
              name="email"
              type="email"
              autocomplete="email"
              required=""
              class="mb-4 w-full rounded border border-gray-500 bg-gray-900"
              placeholder="Emailová adresa"
            />
          </div>

          <div>
            <input
              v-model="password"
              id="password"
              name="password"
              type="password"
              autocomplete="current-password"
              required=""
              class="mb-10 w-full rounded border border-gray-500 bg-gray-900"
              placeholder="Heslo"
            />
          </div>
        </div>

        <!--        <div class="flex items-center justify-between">-->
        <!--          <div class="flex items-center">-->
        <!--            <input id="remember-me" name="remember-me" type="checkbox"-->
        <!--                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"/>-->
        <!--            <label for="remember-me" class="ml-3 block text-base leading-6 text-gray-900">Remember me</label>-->
        <!--          </div>-->

        <!--          <div class="text-base leading-6">-->
        <!--            <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>-->
        <!--          </div>-->
        <!--        </div>-->

        <div>
          <button
            type="submit"
            class="flex w-full justify-center rounded-md bg-gray-200 px-3 py-2 text-base leading-6 text-gray-800 hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Přihlásit se
          </button>
        </div>
      </form>

      <!--      <p class="text-center text-base leading-6 text-gray-500">-->
      <!--        Not a member?-->
      <!--        <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Start a 14-day free trial</a>-->
      <!--      </p>-->
    </div>
  </div>
</template>

<script>
export default {
  setup() {
    definePageMeta({
      layout: "guest",
      middleware: ["sanctum:guest"],
    });

    useHead({
      title: "Přihlášení - Prognola",
    });
  },

  data: () => {
    return {
      email: "",
      password: "",
    };
  },

  methods: {
    async submitLogin() {
      const { login } = useSanctumAuth();

      const userCredentials = {
        email: this.email,
        password: this.password,
      };

      await login(userCredentials);
    },
  },
};
</script>
