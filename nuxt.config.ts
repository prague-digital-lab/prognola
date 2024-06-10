// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    devtools: {enabled: true},
    css: ['~/assets/scss/app.scss'],

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
                baseUrl: 'http://valasskapevnost.cz.test/api', // NUXT_PUBLIC_SANCTUM_BASE_URL=
            },
        }
    },

    modules: ["nuxt-auth-sanctum"]
})