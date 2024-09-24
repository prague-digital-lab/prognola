<template>
  <!--
    This example requires updating your template:

    ```
    <html class="h-full bg-white">
    <body class="h-full">
    ```
  -->
  <div class="flex min-h-full flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">

</div>
</template>


<script>
export default {
  setup() {
    definePageMeta({
      layout: 'guest',
    })

    useHead({
      title: 'Přihlášení - Prognola'
    })
  },

  data: () => {
    return {
      email: '',
      password: ''
    }
  },

  methods: {
    async submitLogin() {
      const {login} = useSanctumAuth();

      const userCredentials = {
        email: this.email,
        password: this.password,
      };

      await login(userCredentials);
    },
  },

}
</script>
