// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    devtools: {enabled: true},
    css: ['~/assets/scss/app.scss'],
    ssr: false,

    postcss: {
        plugins: {
            tailwindcss: {},
            autoprefixer: {},
        },
    },

    runtimeConfig: {
        public: {
            sanctum: {
                baseUrl: 'http://localhost:8000',
                redirect: {
                    onLogin: '/', // Custom route after successful login
                    onAuthOnly: '/login',
                    onGuestOnly: '/',
                    onLogout: '/login',
                },
                endpoints: {
                    login: '/api/login',
                    logout: '/api/logout',
                    user: '/api/user',
                },
                mode: 'token'
            },
        }
    },

    modules: ["nuxt-auth-sanctum"],
    compatibilityDate: '2024-07-16'
})