// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    devtools: {enabled: true},
    css: ['~/assets/scss/app.scss'],

    ssr: true,

    postcss: {
        plugins: {
            tailwindcss: {},
            autoprefixer: {},
        },
    },

    runtimeConfig: {
        public: {
            apiBase: 'http://valasskapevnost.cz.test/api', // NUXT_PUBLIC_API_BASE env
            sanctum: {
                baseUrl: 'http://valasskapevnost.cz.test/', // NUXT_PUBLIC_SANCTUM_BASE_URL=
                redirect: {
                    onLogin: '/', // Custom route after successful login
                    onAuthOnly: '/login',
                    onGuestOnly: '/',
                },
                globalMiddleware: {
                    enabled: true,
                },
            },
        }
    },

    modules: ["nuxt-auth-sanctum"]
})