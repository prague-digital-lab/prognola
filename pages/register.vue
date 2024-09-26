<template>
  <!--
    This example requires updating your template:

    ```
    <html class="h-full bg-white">
    <body class="h-full">
    ```
  -->


  <div class="flex min-h-full flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">


    <div class="w-full max-w-sm space-y-10">

      <div>
        <!--        <img class="mx-auto h-10 w-auto" src="/img/logo_prazska_laborator.png" alt="Logo"/>-->
        <h2 class="mt-10 text-center text-3xl font-bold leading-9 tracking-tight text-gray-200">Vyzkoušejte ještě dnes</h2>
      </div>

      <!--      <div class="border-gray-800 bg-gray-700 text-sm">-->
      <!--        Pro registraci není potřeba zadávat údaje ke kartě.-->
      <!--      </div>-->

      <form class="space-y-6" v-on:submit.prevent="submitRegister">

        <p class="text-gray-200 text-sm mb-0">Emailová adresa</p>
        <div class="relative -space-y-px rounded-md shadow-sm">
          <div class="pointer-events-none absolute inset-0 z-10 rounded-md ring-1 ring-inset ring-gray-300"/>
          <div class="mb-4x">
            <input v-model="email" id="email-address" name="email" type="email" autocomplete="email" required=""
                   class="relative block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-100 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                   placeholder="Emailová adresa"/>
          </div>
        </div>


        <p class="text-gray-200 text-sm mb-0">Heslo</p>
        <input v-model="password" id="password" name="password" type="password" autocomplete="current-password"
               required=""
               class="relative block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-100 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
               placeholder="Heslo"/>

        <input v-model="password_confirmation" id="password" name="password" type="password"
               autocomplete="current-password"
               required=""
               class="relative block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-100 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
               placeholder="Heslo znovu"/>

        <!--        <div class="flex items-center justify-between">-->
        <!--          <div class="flex items-center">-->
        <!--            <input id="remember-me" name="remember-me" type="checkbox"-->
        <!--                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"/>-->
        <!--            <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-900">Remember me</label>-->
        <!--          </div>-->

        <!--          <div class="text-sm leading-6">-->
        <!--            <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>-->
        <!--          </div>-->
        <!--        </div>-->

        <div>
          <button type="submit"
                  v-if="isValid"
                  class="flex w-full justify-center rounded-md bg-gray-200 hover:bg-gray-100 px-3 py-2 text-sm leading-6 text-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Pokračovat
          </button>

          <button type="submit"
                  v-else
                  disabled
                  class="flex w-full justify-center rounded-md bg-gray-500 px-3 py-2 text-sm leading-6 text-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Pokračovat
          </button>
        </div>
      </form>

      <!--      <p class="text-center text-sm leading-6 text-gray-500">-->
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
      layout: 'guest',
      middleware: ['sanctum:guest'],
    })

    useHead({
      title: 'Registrace - Prognola'
    })
  },

  data: () => {
    return {
      email: '',
      password: '',
      password_confirmation: ''
    }
  },

  methods: {
    async submitRegister() {
      const client = useSanctumClient();

      const {data} = await useAsyncData('registration', () =>
          client('/api/register', {
            method: 'POST',
            body: {
              email: this.email,
              password: this.password
            }
          })
      )

      // Login after registration
      const {login} = useSanctumAuth();

      const userCredentials = {
        email: this.email,
        password: this.password,
      };

      await login(userCredentials);
    },

  },

  computed: {
    isValid() {
      return this.password === this.password_confirmation &&
          this.email !== ''
    }
  }

}
</script>
